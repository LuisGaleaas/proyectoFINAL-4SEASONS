<?php

namespace Tests\Feature;

use App\Models\AlternativaInversion;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VarianzasAnualesTest extends TestCase
{
    use DatabaseTransactions;

    private function crearUsuarioYEmpresa(): array
    {
        $user = User::factory()->create();
        $empresa = Empresa::create([
            'nombre' => '4Seasons Test',
            'giro' => 'Comercio',
            'propietario_contacto' => 'Test Owner',
            'telefono' => '7000-0000',
            'tasa_impuesto' => 30.00,
        ]);

        return [$user, $empresa];
    }

    public function test_al_crear_alternativa_se_sincronizan_varianzas_desde_anio_1_hasta_vida_util(): void
    {
        [$user, $empresa] = $this->crearUsuarioYEmpresa();

        $response = $this->actingAs($user)->post(route('admin.alternativas.store'), [
            'empresa_id' => $empresa->id,
            'nombre' => 'Moto Eléctrica',
            'descripcion' => 'Alternativa con flujo variable',
            'inversion_inicial' => 5000,
            'costo_operativo_anual' => 1200,
            'ingreso_anual' => 3000,
            'valor_salvamento' => 500,
            'vida_util_anios' => 4,
            'tasa_tmar' => 12,
            'varianzas' => [
                1 => ['varianza_ingreso_pct' => 10, 'varianza_costo_pct' => -5],
                // Año 2 y 3 omitidos intencionalmente para verificar creación en 0 ("Sin varianza")
                4 => ['varianza_ingreso_pct' => -15, 'varianza_costo_pct' => 20],
            ],
        ]);

        $alt = AlternativaInversion::where('nombre', 'Moto Eléctrica')->firstOrFail();
        $response->assertRedirect(route('admin.alternativas.edit', $alt));

        $varianzas = $alt->varianzasAnuales()->orderBy('anio')->get();
        $this->assertCount(4, $varianzas);
        $this->assertEquals([1, 2, 3, 4], $varianzas->pluck('anio')->all());

        $this->assertSame('10.00', (string) $varianzas[0]->varianza_ingreso_pct);
        $this->assertSame('-5.00', (string) $varianzas[0]->varianza_costo_pct);

        $this->assertSame('0.00', (string) $varianzas[1]->varianza_ingreso_pct);
        $this->assertSame('0.00', (string) $varianzas[1]->varianza_costo_pct);

        $this->assertSame('0.00', (string) $varianzas[2]->varianza_ingreso_pct);
        $this->assertSame('0.00', (string) $varianzas[2]->varianza_costo_pct);

        $this->assertSame('-15.00', (string) $varianzas[3]->varianza_ingreso_pct);
        $this->assertSame('20.00', (string) $varianzas[3]->varianza_costo_pct);
    }

    public function test_al_editar_vida_util_agrega_en_cero_o_elimina_excedentes(): void
    {
        [$user, $empresa] = $this->crearUsuarioYEmpresa();

        $alt = AlternativaInversion::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Reparto Híbrido',
            'descripcion' => 'Prueba de sincronización',
            'inversion_inicial' => 4000,
            'costo_operativo_anual' => 1000,
            'ingreso_anual' => 2500,
            'valor_salvamento' => 400,
            'vida_util_anios' => 3,
            'tasa_tmar' => 12,
        ]);

        $alt->varianzasAnuales()->createMany([
            ['anio' => 1, 'varianza_ingreso_pct' => 5, 'varianza_costo_pct' => 0],
            ['anio' => 2, 'varianza_ingreso_pct' => 10, 'varianza_costo_pct' => 5],
            ['anio' => 3, 'varianza_ingreso_pct' => -5, 'varianza_costo_pct' => 15],
        ]);

        // Aumentar vida útil de 3 a 5 años
        $this->actingAs($user)->put(route('admin.alternativas.update', $alt), [
            'empresa_id' => $empresa->id,
            'nombre' => 'Reparto Híbrido',
            'descripcion' => 'Prueba de sincronización',
            'inversion_inicial' => 4000,
            'costo_operativo_anual' => 1000,
            'ingreso_anual' => 2500,
            'valor_salvamento' => 400,
            'vida_util_anios' => 5,
            'tasa_tmar' => 12,
            'varianzas' => [
                1 => ['varianza_ingreso_pct' => 5, 'varianza_costo_pct' => 0],
                2 => ['varianza_ingreso_pct' => 10, 'varianza_costo_pct' => 5],
                3 => ['varianza_ingreso_pct' => -5, 'varianza_costo_pct' => 15],
            ],
        ])->assertRedirect(route('admin.alternativas.edit', $alt));

        $varianzasExpand = $alt->varianzasAnuales()->orderBy('anio')->get();
        $this->assertCount(5, $varianzasExpand);
        $this->assertSame('0.00', (string) $varianzasExpand[3]->varianza_ingreso_pct);
        $this->assertSame('0.00', (string) $varianzasExpand[4]->varianza_costo_pct);

        // Reducir vida útil de 5 a 2 años (debe eliminar años 3, 4 y 5)
        $this->actingAs($user)->put(route('admin.alternativas.update', $alt), [
            'empresa_id' => $empresa->id,
            'nombre' => 'Reparto Híbrido',
            'descripcion' => 'Prueba de sincronización',
            'inversion_inicial' => 4000,
            'costo_operativo_anual' => 1000,
            'ingreso_anual' => 2500,
            'valor_salvamento' => 400,
            'vida_util_anios' => 2,
            'tasa_tmar' => 12,
            'varianzas' => [
                1 => ['varianza_ingreso_pct' => 5, 'varianza_costo_pct' => 0],
                2 => ['varianza_ingreso_pct' => 10, 'varianza_costo_pct' => 5],
            ],
        ])->assertRedirect(route('admin.alternativas.edit', $alt));

        $varianzasShrink = $alt->varianzasAnuales()->orderBy('anio')->get();
        $this->assertCount(2, $varianzasShrink);
        $this->assertEquals([1, 2], $varianzasShrink->pluck('anio')->all());
    }

    public function test_dashboard_muestra_varianzas_precargadas_y_calculos_irregulares(): void
    {
        [, $empresa] = $this->crearUsuarioYEmpresa();

        $alt = AlternativaInversion::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Alternativa Variable Dashboard',
            'descripcion' => 'Verificación de tablero',
            'inversion_inicial' => 10000,
            'costo_operativo_anual' => 2000,
            'ingreso_anual' => 6000,
            'valor_salvamento' => 1000,
            'vida_util_anios' => 3,
            'tasa_tmar' => 10,
        ]);

        $alt->varianzasAnuales()->createMany([
            ['anio' => 1, 'varianza_ingreso_pct' => 10, 'varianza_costo_pct' => -10],
            ['anio' => 2, 'varianza_ingreso_pct' => 0, 'varianza_costo_pct' => 25],
            ['anio' => 3, 'varianza_ingreso_pct' => -20, 'varianza_costo_pct' => 50],
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Alternativa Variable Dashboard');
        $response->assertSee('6114.20'); // CAUE con flujos irregulares
        $response->assertSee('-1255.00'); // VPN con flujos irregulares
        $response->assertSee('Año 1');
        $response->assertSee('Año 2');
        $response->assertSee('Año 3');
        $response->assertSee('varianzas-sim-body-' . $alt->id);
    }

    public function test_valida_rangos_de_varianza_en_formulario_admin(): void
    {
        [$user, $empresa] = $this->crearUsuarioYEmpresa();

        $response = $this->actingAs($user)->post(route('admin.alternativas.store'), [
            'empresa_id' => $empresa->id,
            'nombre' => 'Alternativa Inválida',
            'descripcion' => 'Varianza fuera de rango',
            'inversion_inicial' => 1000,
            'costo_operativo_anual' => 200,
            'ingreso_anual' => 500,
            'valor_salvamento' => 100,
            'vida_util_anios' => 2,
            'tasa_tmar' => 10,
            'varianzas' => [
                1 => ['varianza_ingreso_pct' => -150, 'varianza_costo_pct' => 1500],
            ],
        ]);

        $response->assertSessionHasErrors([
            'varianzas.1.varianza_ingreso_pct',
            'varianzas.1.varianza_costo_pct',
        ]);
    }
}
