@extends('layouts.app')

@section('title', '4Seasons | ' . ($alternativa->exists ? 'Editar Alternativa' : 'Nueva Alternativa'))

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
    @endphp

    <div class="mx-auto max-w-6xl px-4 pt-6 pb-20 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-linea pb-4">
            <div>
                <p class="text-xs font-semibold tracking-wider text-aqua-600 uppercase">Panel Administrativo</p>
                <h1 class="text-2xl font-semibold text-azul">
                    {{ $alternativa->exists ? 'Editar: ' . $alternativa->nombre : 'Nueva Alternativa de Inversión' }}
                </h1>
            </div>
            <a href="{{ route('admin.index') }}" class="text-sm font-medium text-tinta-suave hover:text-azul">
                ← Volver al panel
            </a>
        </div>

        @if (session('status'))
            <div role="status" class="mb-6 rounded-marca-sm border border-aqua bg-aqua-100/80 px-4 py-3 text-sm font-medium text-azul-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-marca-sm border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800">
                <p class="font-semibold">Corrige los siguientes errores antes de guardar:</p>
                <ul class="mt-1 list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Sección independiente para subir Evidencias Documentales (cuando la alternativa ya existe) --}}
        @if ($alternativa->exists)
            <section class="mb-8 rounded-marca border border-linea bg-blanco p-6 shadow-xs">
                <h2 class="text-lg font-semibold text-azul">Evidencias Documentales Adjuntas</h2>
                <p class="mt-1 text-xs text-tinta-suave">
                    Sube cotizaciones, facturas o entrevistas aquí para poder seleccionarlas como respaldo en los costos y datos clave de abajo.
                </p>

                <form method="POST"
      action="{{ route('admin.evidencias.store', $alternativa) }}"
      enctype="multipart/form-data"
      class="mt-4 space-y-4 rounded-marca-sm border border-linea bg-neutro/50 p-4">
    @csrf

    {{-- Fila 1: Tipo y Descripción --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="ev_tipo" class="mb-1 block text-xs font-medium text-azul">
                Tipo de evidencia
            </label>
            <select id="ev_tipo" name="tipo" required
                    class="w-full rounded-marca-sm border border-linea bg-blanco px-3 py-2 text-xs text-azul focus:border-aqua focus:outline-none">
                @foreach (\App\Models\EvidenciaDocumental::TIPO_CHOICES as $clave => $etiqueta)
                    <option value="{{ $clave }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="ev_descripcion" class="mb-1 block text-xs font-medium text-azul">
                Descripción del documento
            </label>
            <input type="text" id="ev_descripcion" name="descripcion" maxlength="255" required
                   placeholder="Ej: Cotización #104 Honda Navi"
                   class="w-full rounded-marca-sm border border-linea bg-blanco px-3 py-2 text-xs text-azul focus:border-aqua focus:outline-none" />
        </div>
    </div>

    {{-- Fila 2: Selección de archivo y Botón de guardado --}}
    <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
        <div class="min-w-0 md:col-span-2">
            <span class="mb-1 block text-xs font-medium text-azul">1. Seleccionar archivo</span>
            <label for="ev_archivo"
                   class="flex h-10 w-full cursor-pointer items-center justify-between gap-3 overflow-hidden rounded-marca-sm border border-dashed border-azul/40 bg-blanco px-3.5 text-xs text-azul transition hover:border-aqua hover:bg-aqua-100/30">
                <span id="ev_archivo_nombre" class="min-w-0 flex-1 truncate font-medium">
                    📎 Haz clic aquí para elegir PDF o imagen...
                </span>
                <span class="shrink-0 rounded bg-neutro px-2.5 py-1 text-[11px] font-semibold text-azul">
                    Examinar
                </span>
            </label>
            <input type="file"
                   id="ev_archivo"
                   name="archivo"
                   required
                   class="hidden"
                   onchange="document.getElementById('ev_archivo_nombre').textContent = this.files[0] ? '📄 ' + this.files[0].name : '📎 Haz clic aquí para elegir PDF o imagen...'" />
        </div>

        <div>
            <span class="mb-1 block text-xs font-medium text-azul">2. Confirmar subida</span>
            <button type="submit"
                    class="flex h-10 w-full cursor-pointer items-center justify-center gap-1.5 rounded-marca-sm bg-aqua px-4 text-xs font-semibold text-azul-900 shadow-2xs transition hover:bg-aqua-600">
                ⬆ Guardar evidencia
            </button>
        </div>
    </div>
</form>
                </form>

                @if ($evidenciasDisponibles->isNotEmpty())
                    <ul class="mt-4 divide-y divide-linea rounded-marca-sm border border-linea text-xs">
                        @foreach ($evidenciasDisponibles as $ev)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-2.5">
                                <div>
                                    <span class="mr-2 rounded bg-azul px-2 py-0.5 text-[11px] font-medium text-blanco">{{ $ev->tipo }}</span>
                                    <a href="{{ $ev->archivo_url }}" target="_blank" rel="noopener"
                                       class="font-medium text-azul underline decoration-aqua hover:text-aqua-600">
                                        {{ $ev->descripcion }}
                                    </a>
                                    <span class="ml-2 text-tinta-suave">({{ $ev->fecha_registro?->format('d/m/Y') }})</span>
                                </div>
                                <form method="POST" action="{{ route('admin.evidencias.destroy', $ev) }}"
                                      onsubmit="return confirm('¿Eliminar esta evidencia documental?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cursor-pointer text-rose-700 hover:underline">Eliminar</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @else
            <div class="mb-6 rounded-marca-sm border border-azul-100 bg-azul-100/40 px-4 py-3 text-xs text-azul">
                Guarda primero los datos base de la alternativa para habilitar la subida de evidencias documentales adjuntas.
            </div>
        @endif

        {{-- Formulario Principal de AlternativaInversion + Inlines --}}
        <form method="POST"
              action="{{ $alternativa->exists ? route('admin.alternativas.update', $alternativa) : route('admin.alternativas.store') }}"
              class="space-y-8">
            @csrf
            @if ($alternativa->exists)
                @method('PUT')
            @endif

            {{-- Bloque 1: Datos Generales y Parámetros Financieros --}}
            <section class="space-y-5 rounded-marca border border-linea bg-blanco p-6 shadow-xs">
                <h2 class="border-b border-linea pb-2 text-lg font-semibold text-azul">1. Parámetros Financieros Base</h2>

                <div class="grid gap-5 sm:grid-cols-3">
                    <div>
                        <label for="empresa_id" class="mb-1 block text-xs font-medium text-azul">Empresa</label>
                        <select id="empresa_id" name="empresa_id" required
                                class="w-full rounded-marca-sm border border-linea bg-blanco px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none">
                            @foreach ($empresas as $emp)
                                <option value="{{ $emp->id }}" @selected(old('empresa_id', $alternativa->empresa_id) == $emp->id)>
                                    {{ $emp->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="nombre" class="mb-1 block text-xs font-medium text-azul">Nombre de la alternativa</label>
                        <input type="text" id="nombre" name="nombre" maxlength="150" required
                               placeholder="Ej: Adquisición de Moto Propia / Servicio Tercerizado C807"
                               value="{{ old('nombre', $alternativa->nombre) }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label for="descripcion" class="mb-1 block text-xs font-medium text-azul">Descripción operativa</label>
                    <textarea id="descripcion" name="descripcion" rows="2" required
                              class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none">{{ old('descripcion', $alternativa->descripcion) }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <div>
                        <label for="inversion_inicial" class="mb-1 block text-xs font-medium text-azul">Inversión Inicial ($)</label>
                        <input type="number" id="inversion_inicial" name="inversion_inicial" step="0.01" min="0" required
                               value="{{ old('inversion_inicial', $alternativa->inversion_inicial ?? '0.00') }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                    </div>

                    <div>
                        <label for="costo_operativo_anual" class="mb-1 block text-xs font-medium text-azul">Costo Operativo Anual ($)</label>
                        <input type="number" id="costo_operativo_anual" name="costo_operativo_anual" step="0.01" min="0" required
                               value="{{ old('costo_operativo_anual', $alternativa->costo_operativo_anual) }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                    </div>

                    <div>
                        <label for="ingreso_anual" class="mb-1 block text-xs font-medium text-azul">Ingreso / Ahorro Anual ($)</label>
                        <input type="number" id="ingreso_anual" name="ingreso_anual" step="0.01" min="0" required
                               value="{{ old('ingreso_anual', $alternativa->ingreso_anual ?? '0.00') }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                    </div>

                    <div>
                        <label for="vida_util_anios" class="mb-1 block text-xs font-medium text-azul">Vida Útil (Años)</label>
                        <input type="number" id="vida_util_anios" name="vida_util_anios" min="1" max="50" required
                               value="{{ old('vida_util_anios', $alternativa->vida_util_anios ?? 5) }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                    </div>

                    <div>
                        <label for="valor_salvamento" class="mb-1 block text-xs font-medium text-azul">Valor Salvamento ($)</label>
                        <input type="number" id="valor_salvamento" name="valor_salvamento" step="0.01" min="0" required
                               value="{{ old('valor_salvamento', $alternativa->valor_salvamento ?? '0.00') }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                    </div>

                    <div>
                        <label for="tasa_tmar" class="mb-1 block text-xs font-medium text-azul">TMAR (%)</label>
                        <input type="number" id="tasa_tmar" name="tasa_tmar" step="0.01" min="0.01" max="100" required
                               value="{{ old('tasa_tmar', $alternativa->tasa_tmar ?? '12.00') }}"
                               class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                    </div>
                </div>
            </section>

            {{-- Bloque 2: Campos de Auditoría, Proveedores y Trazabilidad --}}
            <section class="space-y-4 rounded-marca border border-linea bg-blanco p-6 shadow-xs">
                <h2 class="border-b border-linea pb-2 text-lg font-semibold text-azul">2. Auditoría, Proveedores y Trazabilidad</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="justificacion_seleccion" class="mb-1 block text-xs font-medium text-azul">
                            ¿Por qué se evaluó esta alternativa?
                        </label>
                        <textarea id="justificacion_seleccion" name="justificacion_seleccion" rows="3"
                                  class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none">{{ old('justificacion_seleccion', $alternativa->justificacion_seleccion) }}</textarea>
                    </div>

                    <div>
                        <label for="detalle_inversion" class="mb-1 block text-xs font-medium text-azul">
                            Desglose y Proveedor de Inversión Inicial
                        </label>
                        <textarea id="detalle_inversion" name="detalle_inversion" rows="3"
                                  class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none">{{ old('detalle_inversion', $alternativa->detalle_inversion) }}</textarea>
                    </div>

                    <div>
                        <label for="detalle_costos_operativos" class="mb-1 block text-xs font-medium text-azul">
                            Desglose de Costos Operativos y Seguros
                        </label>
                        <textarea id="detalle_costos_operativos" name="detalle_costos_operativos" rows="3"
                                  class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none">{{ old('detalle_costos_operativos', $alternativa->detalle_costos_operativos) }}</textarea>
                    </div>

                    <div>
                        <label for="fuentes_verificacion" class="mb-1 block text-xs font-medium text-azul">
                            Fuentes de Verificación (Auditoría)
                        </label>
                        <textarea id="fuentes_verificacion" name="fuentes_verificacion" rows="3"
                                  class="w-full rounded-marca-sm border border-linea px-3 py-2 text-sm text-azul focus:border-aqua focus:outline-none">{{ old('fuentes_verificacion', $alternativa->fuentes_verificacion) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- Bloque 3: Inline Costos por Concepto (CostoConceptoInline) --}}
            <section class="rounded-marca border border-linea bg-blanco p-6 shadow-xs">
                <div class="flex items-center justify-between border-b border-linea pb-2">
                    <div>
                        <h2 class="text-lg font-semibold text-azul">3. Costos por Concepto (Desglose Operativo Anual)</h2>
                        <p class="text-xs text-tinta-suave">La suma de estos rubros debe cuadrar con el Costo Operativo Anual y contar con 2 fuentes.</p>
                    </div>
                    <button type="button" id="btn-add-costo"
                            class="cursor-pointer rounded-marca-sm border border-azul px-3 py-1.5 text-xs font-medium text-azul hover:bg-azul hover:text-blanco">
                        + Agregar rubro
                    </button>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs" id="tabla-costos">
                        <thead class="bg-neutro text-azul">
                            <tr>
                                <th class="px-3 py-2">Concepto</th>
                                <th class="px-3 py-2">Monto anual ($)</th>
                                <th class="px-3 py-2">Evidencia (archivo)</th>
                                <th class="px-3 py-2">Otra fuente (nota)</th>
                                <th class="px-3 py-2 text-center">Quitar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-linea">
                            @foreach ($costosIniciales as $idx => $costo)
                                <tr>
                                    <td class="p-2">
                                        <input type="hidden" name="costos[{{ $idx }}][id]" value="{{ $costo['id'] ?? '' }}" />
                                        <input type="text" name="costos[{{ $idx }}][concepto]" maxlength="150"
                                               value="{{ $costo['concepto'] ?? '' }}"
                                               placeholder="Ej: Gasolina, Seguro, Mantenimiento"
                                               class="w-full rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul" />
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="costos[{{ $idx }}][monto_anual]" step="0.01" min="0"
                                               value="{{ $costo['monto_anual'] ?? '' }}"
                                               class="w-28 rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul tabular-nums" />
                                    </td>
                                    <td class="p-2">
                                        <select name="costos[{{ $idx }}][fuente_evidencia_id]"
                                                class="w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-xs text-azul">
                                            <option value="">-- Ninguna --</option>
                                            @foreach ($evidenciasDisponibles as $ev)
                                                <option value="{{ $ev->id }}" @selected(($costo['fuente_evidencia_id'] ?? '') == $ev->id)>
                                                    {{ $ev->tipo }}: {{ $ev->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="text" name="costos[{{ $idx }}][fuente_nota]" maxlength="255"
                                               value="{{ $costo['fuente_nota'] ?? '' }}"
                                               placeholder="Ej: Entrevista con el propietario"
                                               class="w-full rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul" />
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" class="btn-remove-row cursor-pointer text-rose-700 hover:underline">✕</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Bloque 4: Inline Fuentes de Datos Clave (FuenteDatoInline) --}}
            <section class="rounded-marca border border-linea bg-blanco p-6 shadow-xs">
                <div class="flex items-center justify-between border-b border-linea pb-2">
                    <div>
                        <h2 class="text-lg font-semibold text-azul">4. Fuentes de Datos Clave</h2>
                        <p class="text-xs text-tinta-suave">Respalda cada parámetro financiero (Inversión, Ingreso, Vida útil, TMAR) con al menos 2 fuentes.</p>
                    </div>
                    <button type="button" id="btn-add-fuente"
                            class="cursor-pointer rounded-marca-sm border border-azul px-3 py-1.5 text-xs font-medium text-azul hover:bg-azul hover:text-blanco">
                        + Agregar fuente
                    </button>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs" id="tabla-fuentes">
                        <thead class="bg-neutro text-azul">
                            <tr>
                                <th class="px-3 py-2">Dato clave</th>
                                <th class="px-3 py-2">Evidencia (archivo)</th>
                                <th class="px-3 py-2">Otra fuente (nota)</th>
                                <th class="px-3 py-2 text-center">Quitar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-linea">
                            @foreach ($fuentesIniciales as $idx => $fuente)
                                <tr>
                                    <td class="p-2">
                                        <input type="hidden" name="fuentes[{{ $idx }}][id]" value="{{ $fuente['id'] ?? '' }}" />
                                        <select name="fuentes[{{ $idx }}][dato]"
                                                class="w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-xs text-azul">
                                            <option value="">-- Seleccionar dato --</option>
                                            @foreach (\App\Models\FuenteDato::DATO_CHOICES as $clave => $etiqueta)
                                                <option value="{{ $clave }}" @selected(($fuente['dato'] ?? '') === $clave)>
                                                    {{ $etiqueta }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <select name="fuentes[{{ $idx }}][fuente_evidencia_id]"
                                                class="w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-xs text-azul">
                                            <option value="">-- Ninguna --</option>
                                            @foreach ($evidenciasDisponibles as $ev)
                                                <option value="{{ $ev->id }}" @selected(($fuente['fuente_evidencia_id'] ?? '') == $ev->id)>
                                                    {{ $ev->tipo }}: {{ $ev->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="text" name="fuentes[{{ $idx }}][fuente_nota]" maxlength="255"
                                               value="{{ $fuente['fuente_nota'] ?? '' }}"
                                               placeholder="Ej: Entrevista con el propietario, 12/11/2026"
                                               class="w-full rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul" />
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" class="btn-remove-row cursor-pointer text-rose-700 hover:underline">✕</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.index') }}"
                   class="rounded-marca-sm border border-linea bg-blanco px-5 py-2.5 text-sm font-medium text-tinta-suave hover:text-azul">
                    Cancelar
                </a>
                <button type="submit"
                        class="cursor-pointer rounded-marca-sm bg-azul px-6 py-2.5 text-sm font-medium text-blanco hover:bg-azul-700">
                    Guardar Alternativa de Inversión
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const evidenciasOptions = `
                <option value="">-- Ninguna --</option>
                @foreach ($evidenciasDisponibles as $ev)
                    <option value="{{ $ev->id }}">{{ addslashes($ev->tipo . ': ' . $ev->descripcion) }}</option>
                @endforeach
            `;

            const datosOptions = `
                <option value="">-- Seleccionar dato --</option>
                @foreach (\App\Models\FuenteDato::DATO_CHOICES as $clave => $etiqueta)
                    <option value="{{ $clave }}">{{ addslashes($etiqueta) }}</option>
                @endforeach
            `;

            let costoIdx = document.querySelectorAll("#tabla-costos tbody tr").length;
            document.getElementById("btn-add-costo").addEventListener("click", function () {
                const tbody = document.querySelector("#tabla-costos tbody");
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="p-2">
                        <input type="hidden" name="costos[${costoIdx}][id]" value="" />
                        <input type="text" name="costos[${costoIdx}][concepto]" maxlength="150" placeholder="Ej: Gasolina, Seguro" class="w-full rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul" />
                    </td>
                    <td class="p-2">
                        <input type="number" name="costos[${costoIdx}][monto_anual]" step="0.01" min="0" class="w-28 rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul tabular-nums" />
                    </td>
                    <td class="p-2">
                        <select name="costos[${costoIdx}][fuente_evidencia_id]" class="w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-xs text-azul">${evidenciasOptions}</select>
                    </td>
                    <td class="p-2">
                        <input type="text" name="costos[${costoIdx}][fuente_nota]" maxlength="255" placeholder="Ej: Entrevista" class="w-full rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul" />
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" class="btn-remove-row cursor-pointer text-rose-700 hover:underline">✕</button>
                    </td>
                `;
                tbody.appendChild(tr);
                costoIdx++;
            });

           let fuenteIdx = document.querySelectorAll("#tabla-fuentes tbody tr").length;
            document.getElementById("btn-add-fuente").addEventListener("click", function () {
                const tbody = document.querySelector("#tabla-fuentes tbody");
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="p-2">
                        <input type="hidden" name="fuentes[${fuenteIdx}][id]" value="" />
                        <select name="fuentes[${fuenteIdx}][dato]" class="w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-xs text-azul">${datosOptions}</select>
                    </td>
                    <td class="p-2">
                        <select name="fuentes[${fuenteIdx}][fuente_evidencia_id]" class="w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-xs text-azul">${evidenciasOptions}</select>
                    </td>
                    <td class="p-2">
                        <input type="text" name="fuentes[${fuenteIdx}][fuente_nota]" maxlength="255" placeholder="Ej: Entrevista" class="w-full rounded-marca-sm border border-linea px-2.5 py-1.5 text-xs text-azul" />
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" class="btn-remove-row cursor-pointer text-rose-700 hover:underline">✕</button>
                    </td>
                `;
                tbody.appendChild(tr);
                fuenteIdx++;
            });

            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("btn-remove-row")) {
                    e.target.closest("tr").remove();
                }
            });
        });
    </script>
@endsection