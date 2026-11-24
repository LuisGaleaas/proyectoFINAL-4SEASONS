@extends('layouts.app')

@section('title', '4Seasons | ' . ($alternativa->exists ? 'Editar alternativa' : 'Nueva alternativa'))

@section('content')
    @php
        $evidenciasDisponibles = $alternativa->exists ? $alternativa->evidencias : collect();

        $costosIniciales = old('costos');
        if (is_null($costosIniciales)) {
            $costosIniciales = $alternativa->exists
                ? $alternativa->costos->map(fn ($c) => [
                    'id' => $c->id,
                    'concepto' => $c->concepto,
                    'monto_anual' => $c->monto_anual,
                    'fuente_evidencia_id' => $c->fuente_evidencia_id,
                    'fuente_nota' => $c->fuente_nota,
                ])->toArray()
                : [];
            $costosIniciales[] = ['id' => '', 'concepto' => '', 'monto_anual' => '', 'fuente_evidencia_id' => '', 'fuente_nota' => ''];
        }

        $fuentesIniciales = old('fuentes');
        if (is_null($fuentesIniciales)) {
            $fuentesIniciales = $alternativa->exists
                ? $alternativa->fuentesDatos->map(fn ($f) => [
                    'id' => $f->id,
                    'dato' => $f->dato,
                    'fuente_evidencia_id' => $f->fuente_evidencia_id,
                    'fuente_nota' => $f->fuente_nota,
                ])->toArray()
                : [];
            $fuentesIniciales[] = ['id' => '', 'dato' => '', 'fuente_evidencia_id' => '', 'fuente_nota' => ''];
        }

        $parametros = [
            ['campo' => 'inversion_inicial', 'etiqueta' => 'Inversión inicial', 'antes' => '$', 'despues' => null, 'step' => '0.01', 'min' => '0', 'max' => null, 'defecto' => '0.00'],
            ['campo' => 'costo_operativo_anual', 'etiqueta' => 'Costo operativo anual', 'antes' => '$', 'despues' => null, 'step' => '0.01', 'min' => '0', 'max' => null, 'defecto' => null],
            ['campo' => 'ingreso_anual', 'etiqueta' => 'Ingreso o ahorro anual', 'antes' => '$', 'despues' => null, 'step' => '0.01', 'min' => '0', 'max' => null, 'defecto' => '0.00'],
            ['campo' => 'valor_salvamento', 'etiqueta' => 'Valor de salvamento', 'antes' => '$', 'despues' => null, 'step' => '0.01', 'min' => '0', 'max' => null, 'defecto' => '0.00'],
            ['campo' => 'vida_util_anios', 'etiqueta' => 'Vida útil', 'antes' => null, 'despues' => 'años', 'step' => null, 'min' => '1', 'max' => '50', 'defecto' => 5],
            ['campo' => 'tasa_tmar', 'etiqueta' => 'TMAR', 'antes' => null, 'despues' => '%', 'step' => '0.01', 'min' => '0.01', 'max' => '100', 'defecto' => '12.00'],
        ];
    @endphp

    <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:px-8">
        <a href="{{ route('admin.index') }}" class="btn btn-fantasma btn-chico -ml-3">
            <x-icono nombre="flecha-izq" class="size-4" />
            Volver al panel
        </a>

        <h1 class="mt-4 text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
            {{ $alternativa->exists ? 'Editar: ' . $alternativa->nombre : 'Nueva alternativa de inversión' }}
        </h1>
        <p class="mt-2 text-[15px] text-tinta-suave">Panel administrativo</p>

        @if (session('status'))
            <div role="status" class="aviso mt-6 bg-exito-100 font-medium text-exito">
                <x-icono nombre="check" class="mt-0.5 size-5" />
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="aviso mt-6 bg-peligro-100 text-peligro">
                <x-icono nombre="alerta" class="mt-0.5 size-5" />
                <div>
                    <p class="font-semibold">Corrige los siguientes errores antes de guardar:</p>
                    <ul class="mt-1.5 list-disc space-y-1 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if ($alternativa->exists)
            <section aria-labelledby="titulo-evidencias" class="mt-10 grid gap-6 border-t border-linea pt-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <h2 id="titulo-evidencias" class="text-xl font-semibold text-azul">Evidencias documentales adjuntas</h2>
                    <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                        Sube cotizaciones, facturas o entrevistas aquí para poder seleccionarlas como respaldo en los costos y datos clave de abajo.
                    </p>
                </div>

                <div class="min-w-0 space-y-5 lg:col-span-8">
                    <form method="POST"
                          action="{{ route('admin.evidencias.store', $alternativa) }}"
                          enctype="multipart/form-data"
                          class="rounded-marca-lg border border-linea bg-blanco p-6">
                        @csrf

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="ev_tipo" class="etiqueta">Tipo de evidencia</label>
                                <select id="ev_tipo" name="tipo" required class="campo">
                                    @foreach (\App\Models\EvidenciaDocumental::TIPO_CHOICES as $clave => $etiqueta)
                                        <option value="{{ $clave }}">{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="ev_descripcion" class="etiqueta">Descripción del documento</label>
                                <input type="text" id="ev_descripcion" name="descripcion" maxlength="255" required
                                       placeholder="Ej: Cotización #104 Honda Navi"
                                       class="campo" />
                            </div>
                        </div>

                        <div class="mt-5">
                            <span class="etiqueta">Archivo</span>
                            <input type="file"
                                   id="ev_archivo"
                                   name="archivo"
                                   required
                                   class="peer sr-only"
                                   onchange="document.getElementById('ev_archivo_nombre').textContent = this.files[0] ? this.files[0].name : 'Haz clic aquí para elegir PDF o imagen'" />
                            <label for="ev_archivo"
                                   class="flex min-h-20 w-full cursor-pointer items-center gap-4 rounded-marca border border-dashed border-azul/35 bg-neutro/50 px-5 py-4 text-azul transition-[border-color,background-color] duration-150 hover:border-aqua-600 hover:bg-aqua-100/40 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-3 peer-focus-visible:outline-azul">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-marca-sm bg-blanco text-aqua-600">
                                    <x-icono nombre="archivo" class="size-5" />
                                </span>
                                <span id="ev_archivo_nombre" class="min-w-0 flex-1 truncate text-[15px] font-medium">
                                    Haz clic aquí para elegir PDF o imagen
                                </span>
                                <span class="hidden shrink-0 text-sm font-medium text-tinta-suave underline decoration-aqua decoration-2 underline-offset-4 sm:inline">
                                    Examinar
                                </span>
                            </label>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit" class="btn btn-primario">
                                <x-icono nombre="subir" class="size-5" />
                                Guardar evidencia
                            </button>
                        </div>
                    </form>

                    @if ($evidenciasDisponibles->isNotEmpty())
                        <ul class="divide-y divide-linea rounded-marca border border-linea bg-blanco">
                            @foreach ($evidenciasDisponibles as $ev)
                                <li class="flex flex-wrap items-center gap-x-4 gap-y-2 px-5 py-3.5">
                                    <x-icono nombre="archivo" class="size-5 text-tinta-suave" />
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $ev->archivo_url }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 font-medium text-azul underline decoration-aqua decoration-1 underline-offset-4 hover:decoration-2">
                                            {{ $ev->descripcion }}
                                            <x-icono nombre="externo" class="size-3.5" />
                                        </a>
                                        <p class="mt-0.5 text-sm text-tinta-suave">
                                            {{ $ev->tipo }}
                                            <span class="sr-only">, registrada el</span>
                                            <time class="ml-2 tabular-nums">{{ $ev->fecha_registro?->format('d/m/Y') }}</time>
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.evidencias.destroy', $ev) }}"
                                          onsubmit="return confirm('¿Eliminar esta evidencia documental?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-fantasma btn-chico hover:bg-peligro-100 hover:text-peligro">
                                            <x-icono nombre="basura" class="size-4" />
                                            Eliminar
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        @else
            <div class="aviso mt-8 bg-azul-100/60 text-azul">
                <x-icono nombre="archivo" class="mt-0.5 size-5" />
                <p>Guarda primero los datos base de la alternativa para habilitar la subida de evidencias documentales adjuntas.</p>
            </div>
        @endif

        <form method="POST"
              action="{{ $alternativa->exists ? route('admin.alternativas.update', $alternativa) : route('admin.alternativas.store') }}">
            @csrf
            @if ($alternativa->exists)
                @method('PUT')
            @endif

            {{-- Parámetros financieros base --}}
            <section aria-labelledby="titulo-parametros" class="mt-10 grid gap-6 border-t border-linea pt-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <h2 id="titulo-parametros" class="text-xl font-semibold text-azul">Parámetros financieros base</h2>
                </div>

                <div class="min-w-0 space-y-6 rounded-marca-lg border border-linea bg-blanco p-6 lg:col-span-8">
                    <div class="grid gap-5 sm:grid-cols-3">
                        <div>
                            <label for="empresa_id" class="etiqueta">Empresa</label>
                            <select id="empresa_id" name="empresa_id" required
                                    @error('empresa_id') aria-invalid="true" @enderror
                                    class="campo">
                                @foreach ($empresas as $emp)
                                    <option value="{{ $emp->id }}" @selected(old('empresa_id', $alternativa->empresa_id) == $emp->id)>
                                        {{ $emp->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="nombre" class="etiqueta">Nombre de la alternativa</label>
                            <input type="text" id="nombre" name="nombre" maxlength="150" required
                                   placeholder="Ej: Adquisición de Moto Propia / Servicio Tercerizado C807"
                                   value="{{ old('nombre', $alternativa->nombre) }}"
                                   @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
                                   class="campo" />
                            @error('nombre') <p id="nombre-error" class="error-campo">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="descripcion" class="etiqueta">Descripción operativa</label>
                        <textarea id="descripcion" name="descripcion" rows="2" required
                                  @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror
                                  class="campo">{{ old('descripcion', $alternativa->descripcion) }}</textarea>
                        @error('descripcion') <p id="descripcion-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-x-5 gap-y-6 border-t border-linea pt-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($parametros as $p)
                            <div>
                                <label for="{{ $p['campo'] }}" class="etiqueta">{{ $p['etiqueta'] }}</label>
                                <div class="campo-unidad">
                                    @if ($p['antes'])
                                        <span>{{ $p['antes'] }}</span>
                                    @endif
                                    <input type="number" id="{{ $p['campo'] }}" name="{{ $p['campo'] }}" required
                                           @if ($p['step']) step="{{ $p['step'] }}" @endif
                                           min="{{ $p['min'] }}"
                                           @if ($p['max']) max="{{ $p['max'] }}" @endif
                                           value="{{ old($p['campo'], $alternativa->{$p['campo']} ?? $p['defecto']) }}"
                                           @error($p['campo']) aria-invalid="true" aria-describedby="{{ $p['campo'] }}-error" @enderror
                                           class="campo tabular-nums" />
                                    @if ($p['despues'])
                                        <span>{{ $p['despues'] }}</span>
                                    @endif
                                </div>
                                @error($p['campo']) <p id="{{ $p['campo'] }}-error" class="error-campo">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section aria-labelledby="titulo-auditoria" class="mt-10 grid gap-6 border-t border-linea pt-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <h2 id="titulo-auditoria" class="text-xl font-semibold text-azul">Auditoría, proveedores y trazabilidad</h2>
                </div>

                <div class="grid min-w-0 gap-6 rounded-marca-lg border border-linea bg-blanco p-6 sm:grid-cols-2 lg:col-span-8">
                    <div>
                        <label for="justificacion_seleccion" class="etiqueta">¿Por qué se evaluó esta alternativa?</label>
                        <textarea id="justificacion_seleccion" name="justificacion_seleccion" rows="3"
                                  class="campo">{{ old('justificacion_seleccion', $alternativa->justificacion_seleccion) }}</textarea>
                    </div>

                    <div>
                        <label for="detalle_inversion" class="etiqueta">Desglose y proveedor de la inversión inicial</label>
                        <textarea id="detalle_inversion" name="detalle_inversion" rows="3"
                                  class="campo">{{ old('detalle_inversion', $alternativa->detalle_inversion) }}</textarea>
                    </div>

                    <div>
                        <label for="detalle_costos_operativos" class="etiqueta">Desglose de costos operativos y seguros</label>
                        <textarea id="detalle_costos_operativos" name="detalle_costos_operativos" rows="3"
                                  class="campo">{{ old('detalle_costos_operativos', $alternativa->detalle_costos_operativos) }}</textarea>
                    </div>

                    <div>
                        <label for="fuentes_verificacion" class="etiqueta">Fuentes de verificación (auditoría)</label>
                        <textarea id="fuentes_verificacion" name="fuentes_verificacion" rows="3"
                                  class="campo">{{ old('fuentes_verificacion', $alternativa->fuentes_verificacion) }}</textarea>
                    </div>
                </div>
            </section>

            <section aria-labelledby="titulo-costos" class="mt-10 border-t border-linea pt-10">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="max-w-2xl">
                        <h2 id="titulo-costos" class="text-xl font-semibold text-azul">Costos por concepto</h2>
                        <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                            Desglose operativo anual. La suma de estos rubros debe cuadrar con el costo operativo anual y contar con 2 fuentes.
                        </p>
                    </div>
                    <button type="button" id="btn-add-costo" class="btn btn-secundario">
                        <x-icono nombre="mas" class="size-5" />
                        Agregar rubro
                    </button>
                </div>

                <div class="mt-6 overflow-x-auto rounded-marca-lg border border-linea bg-blanco">
                    <table class="w-full min-w-[48rem] border-collapse text-left" id="tabla-costos">
                        <thead class="border-b border-linea text-sm text-tinta-suave">
                            <tr>
                                <th scope="col" class="px-3 py-3 pl-5 font-medium">Concepto</th>
                                <th scope="col" class="w-40 px-3 py-3 font-medium">Monto anual</th>
                                <th scope="col" class="px-3 py-3 font-medium">Evidencia (archivo)</th>
                                <th scope="col" class="px-3 py-3 font-medium">Otra fuente (nota)</th>
                                <th scope="col" class="w-14 px-3 py-3 pr-5"><span class="sr-only">Quitar</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-linea">
                            @foreach ($costosIniciales as $idx => $costo)
                                <tr>
                                    <td class="px-3 py-2.5 pl-5">
                                        <input type="hidden" name="costos[{{ $idx }}][id]" value="{{ $costo['id'] ?? '' }}" />
                                        <input type="text" name="costos[{{ $idx }}][concepto]" maxlength="150"
                                               value="{{ $costo['concepto'] ?? '' }}"
                                               placeholder="Ej: Gasolina, Seguro, Mantenimiento"
                                               aria-label="Concepto"
                                               class="campo campo-compacto" />
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="campo-unidad">
                                            <span>$</span>
                                            <input type="number" name="costos[{{ $idx }}][monto_anual]" step="0.01" min="0"
                                                   value="{{ $costo['monto_anual'] ?? '' }}"
                                                   aria-label="Monto anual"
                                                   class="campo campo-compacto tabular-nums" />
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <select name="costos[{{ $idx }}][fuente_evidencia_id]" aria-label="Evidencia (archivo)"
                                                class="campo campo-compacto">
                                            <option value="">Ninguna</option>
                                            @foreach ($evidenciasDisponibles as $ev)
                                                <option value="{{ $ev->id }}" @selected(($costo['fuente_evidencia_id'] ?? '') == $ev->id)>
                                                    {{ $ev->tipo }}: {{ $ev->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <input type="text" name="costos[{{ $idx }}][fuente_nota]" maxlength="255"
                                               value="{{ $costo['fuente_nota'] ?? '' }}"
                                               placeholder="Ej: Entrevista con el propietario"
                                               aria-label="Otra fuente (nota)"
                                               class="campo campo-compacto" />
                                    </td>
                                    <td class="px-3 py-2.5 pr-5 text-right">
                                        <button type="button" class="btn-remove-row btn-icono" aria-label="Quitar fila">
                                            <x-icono nombre="cerrar" class="size-4" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>


            <section aria-labelledby="titulo-fuentes" class="mt-10 border-t border-linea pt-10">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="max-w-2xl">
                        <h2 id="titulo-fuentes" class="text-xl font-semibold text-azul">Fuentes de datos clave</h2>
                        <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                            Respalda cada parámetro financiero (inversión, ingreso, vida útil, TMAR) con al menos 2 fuentes.
                        </p>
                    </div>
                    <button type="button" id="btn-add-fuente" class="btn btn-secundario">
                        <x-icono nombre="mas" class="size-5" />
                        Agregar fuente
                    </button>
                </div>

                <div class="mt-6 overflow-x-auto rounded-marca-lg border border-linea bg-blanco">
                    <table class="w-full min-w-[44rem] border-collapse text-left" id="tabla-fuentes">
                        <thead class="border-b border-linea text-sm text-tinta-suave">
                            <tr>
                                <th scope="col" class="w-64 px-3 py-3 pl-5 font-medium">Dato clave</th>
                                <th scope="col" class="px-3 py-3 font-medium">Evidencia (archivo)</th>
                                <th scope="col" class="px-3 py-3 font-medium">Otra fuente (nota)</th>
                                <th scope="col" class="w-14 px-3 py-3 pr-5"><span class="sr-only">Quitar</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-linea">
                            @foreach ($fuentesIniciales as $idx => $fuente)
                                <tr>
                                    <td class="px-3 py-2.5 pl-5">
                                        <input type="hidden" name="fuentes[{{ $idx }}][id]" value="{{ $fuente['id'] ?? '' }}" />
                                        <select name="fuentes[{{ $idx }}][dato]" aria-label="Dato clave"
                                                class="campo campo-compacto">
                                            <option value="">Seleccionar dato</option>
                                            @foreach (\App\Models\FuenteDato::DATO_CHOICES as $clave => $etiqueta)
                                                <option value="{{ $clave }}" @selected(($fuente['dato'] ?? '') === $clave)>
                                                    {{ $etiqueta }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <select name="fuentes[{{ $idx }}][fuente_evidencia_id]" aria-label="Evidencia (archivo)"
                                                class="campo campo-compacto">
                                            <option value="">Ninguna</option>
                                            @foreach ($evidenciasDisponibles as $ev)
                                                <option value="{{ $ev->id }}" @selected(($fuente['fuente_evidencia_id'] ?? '') == $ev->id)>
                                                    {{ $ev->tipo }}: {{ $ev->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <input type="text" name="fuentes[{{ $idx }}][fuente_nota]" maxlength="255"
                                               value="{{ $fuente['fuente_nota'] ?? '' }}"
                                               placeholder="Ej: Entrevista con el propietario, 12/11/2026"
                                               aria-label="Otra fuente (nota)"
                                               class="campo campo-compacto" />
                                    </td>
                                    <td class="px-3 py-2.5 pr-5 text-right">
                                        <button type="button" class="btn-remove-row btn-icono" aria-label="Quitar fila">
                                            <x-icono nombre="cerrar" class="size-4" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Barra de acciones fija al pie mientras se edita --}}
            <div class="sticky bottom-0 z-10 -mx-4 mt-12 border-t border-linea bg-neutro/95 px-4 py-4 backdrop-blur-sm sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.index') }}" class="btn btn-secundario">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primario px-6">
                        <x-icono nombre="check" class="size-5" />
                        Guardar alternativa de inversión
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const evidenciasOptions = `
                <option value="">Ninguna</option>
                @foreach ($evidenciasDisponibles as $ev)
                    <option value="{{ $ev->id }}">{{ addslashes($ev->tipo . ': ' . $ev->descripcion) }}</option>
                @endforeach
            `;

            const datosOptions = `
                <option value="">Seleccionar dato</option>
                @foreach (\App\Models\FuenteDato::DATO_CHOICES as $clave => $etiqueta)
                    <option value="{{ $clave }}">{{ addslashes($etiqueta) }}</option>
                @endforeach
            `;

            // Mismo botón e ícono que las filas renderizadas en el servidor.
            const botonQuitar = `
                <button type="button" class="btn-remove-row btn-icono" aria-label="Quitar fila">
                    <svg class="pointer-events-none size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            `;

            let costoIdx = document.querySelectorAll("#tabla-costos tbody tr").length;
            document.getElementById("btn-add-costo").addEventListener("click", function () {
                const tbody = document.querySelector("#tabla-costos tbody");
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="px-3 py-2.5 pl-5">
                        <input type="hidden" name="costos[${costoIdx}][id]" value="" />
                        <input type="text" name="costos[${costoIdx}][concepto]" maxlength="150" placeholder="Ej: Gasolina, Seguro" aria-label="Concepto" class="campo campo-compacto" />
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="campo-unidad">
                            <span>$</span>
                            <input type="number" name="costos[${costoIdx}][monto_anual]" step="0.01" min="0" aria-label="Monto anual" class="campo campo-compacto tabular-nums" />
                        </div>
                    </td>
                    <td class="px-3 py-2.5">
                        <select name="costos[${costoIdx}][fuente_evidencia_id]" aria-label="Evidencia (archivo)" class="campo campo-compacto">${evidenciasOptions}</select>
                    </td>
                    <td class="px-3 py-2.5">
                        <input type="text" name="costos[${costoIdx}][fuente_nota]" maxlength="255" placeholder="Ej: Entrevista" aria-label="Otra fuente (nota)" class="campo campo-compacto" />
                    </td>
                    <td class="px-3 py-2.5 pr-5 text-right">${botonQuitar}</td>
                `;
                tbody.appendChild(tr);
                tr.querySelector("input[type=text]").focus();
                costoIdx++;
            });

            let fuenteIdx = document.querySelectorAll("#tabla-fuentes tbody tr").length;
            document.getElementById("btn-add-fuente").addEventListener("click", function () {
                const tbody = document.querySelector("#tabla-fuentes tbody");
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="px-3 py-2.5 pl-5">
                        <input type="hidden" name="fuentes[${fuenteIdx}][id]" value="" />
                        <select name="fuentes[${fuenteIdx}][dato]" aria-label="Dato clave" class="campo campo-compacto">${datosOptions}</select>
                    </td>
                    <td class="px-3 py-2.5">
                        <select name="fuentes[${fuenteIdx}][fuente_evidencia_id]" aria-label="Evidencia (archivo)" class="campo campo-compacto">${evidenciasOptions}</select>
                    </td>
                    <td class="px-3 py-2.5">
                        <input type="text" name="fuentes[${fuenteIdx}][fuente_nota]" maxlength="255" placeholder="Ej: Entrevista" aria-label="Otra fuente (nota)" class="campo campo-compacto" />
                    </td>
                    <td class="px-3 py-2.5 pr-5 text-right">${botonQuitar}</td>
                `;
                tbody.appendChild(tr);
                tr.querySelector("select").focus();
                fuenteIdx++;
            });

            document.addEventListener("click", function (e) {
                const boton = e.target.closest(".btn-remove-row");
                if (boton) {
                    boton.closest("tr").remove();
                }
            });
        });
    </script>
@endsection
