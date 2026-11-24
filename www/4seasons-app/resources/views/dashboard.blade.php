@extends('layouts.app')

@section('title', '4Seasons | Tablero de Evaluación Económica')

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-20 sm:px-6 lg:px-8">
        {{-- Encabezado del tablero --}}
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-linea pb-6">
            <div>
                <p class="text-sm font-medium text-aqua-600">
                    Proyecto Integrador UES-FMO · Sistemas Contables e Ingeniería Económica
                </p>
                <h1 class="mt-1 text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
                    {{ $empresa->nombre ?? '4Seasons' }} — Evaluación Económica de Inversiones
                </h1>
                @if ($empresa?->giro)
                    <p class="mt-1 text-sm text-tinta-suave">{{ $empresa->giro }}</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('inicio') }}"
                   class="inline-flex min-h-10 items-center rounded-marca-sm border border-azul/25 bg-blanco px-4 text-sm font-medium text-azul transition hover:border-azul">
                    ← Inicio
                </a>
                <a href="{{ url('/admin') }}"
                   class="inline-flex min-h-10 items-center rounded-marca-sm bg-azul px-4 text-sm font-medium text-blanco transition hover:bg-azul-700">
                    Panel Admin
                </a>
            </div>
        </div>

        {{-- Alerta de recomendación del sistema --}}
        @if ($mejor_opcion)
            <div role="status" class="mb-8 rounded-marca border border-aqua bg-aqua-100/80 p-5 text-azul-900">
                <p class="text-xs font-semibold tracking-wider text-aqua-600 uppercase">Recomendación del Sistema</p>
                <p class="mt-1 text-base leading-relaxed">
                    Según el análisis de <strong>Costo Anual Uniforme Equivalente (CAUE)</strong>, la opción más viable económicamente es:
                    <strong class="font-semibold underline decoration-aqua decoration-2 underline-offset-4">{{ $mejor_opcion }}</strong>.
                </p>
            </div>
        @endif

        {{-- Grid comparativo de alternativas --}}
        <div class="grid gap-8 lg:grid-cols-2">
            @forelse ($resultados as $res)
                @php
                    /** @var \App\Models\AlternativaInversion $alt */
                    $alt = $res['objeto'];
                    $tasaIsr = $empresa ? (float) $empresa->tasa_impuesto : 30.00;
                @endphp

                <article class="flex flex-col overflow-hidden rounded-marca border border-linea bg-blanco shadow-xs">
                    {{-- Cabecera de tarjeta --}}
                    <header class="bg-azul px-6 py-4 text-blanco">
                        <h2 class="text-xl font-semibold">{{ $alt->nombre }}</h2>
                    </header>

                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-sm leading-relaxed text-tinta-suave">{{ $alt->descripcion }}</p>

                        {{-- Simulador de Variables en Tiempo Real --}}
                        <div class="mt-5 rounded-marca-sm border border-linea bg-neutro/70 p-4">
                            <h3 class="text-sm font-semibold text-azul">Simulador de Variables (Tiempo Real)</h3>

                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div>
                                    <label for="inv-{{ $alt->id }}" class="mb-1 block text-xs font-medium text-tinta-suave">
                                        Inversión ($)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        id="inv-{{ $alt->id }}"
                                        value="{{ number_format((float) $alt->inversion_inicial, 2, '.', '') }}"
                                        data-id="{{ $alt->id }}"
                                        class="calc-trigger w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-sm font-medium text-azul tabular-nums focus:border-aqua focus:outline-none"
                                    />
                                </div>

                                <div>
                                    <label for="ing-{{ $alt->id }}" class="mb-1 block text-xs font-medium text-tinta-suave">
                                        Ingreso/Ahorro ($)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        id="ing-{{ $alt->id }}"
                                        value="{{ number_format((float) $alt->ingreso_anual, 2, '.', '') }}"
                                        data-id="{{ $alt->id }}"
                                        class="calc-trigger w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-sm font-medium text-azul tabular-nums focus:border-aqua focus:outline-none"
                                    />
                                </div>

                                <div>
                                    <label for="costo-{{ $alt->id }}" class="mb-1 block text-xs font-medium text-tinta-suave">
                                        Costo Anual ($)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        id="costo-{{ $alt->id }}"
                                        value="{{ number_format((float) $alt->costo_operativo_anual, 2, '.', '') }}"
                                        data-id="{{ $alt->id }}"
                                        class="calc-trigger w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-sm font-medium text-azul tabular-nums focus:border-aqua focus:outline-none"
                                    />
                                </div>

                                <div>
                                    <label for="vida-{{ $alt->id }}" class="mb-1 block text-xs font-medium text-tinta-suave">
                                        Vida (Años)
                                    </label>
                                    <input
                                        type="number"
                                        min="1"
                                        max="50"
                                        id="vida-{{ $alt->id }}"
                                        value="{{ $alt->vida_util_anios }}"
                                        data-id="{{ $alt->id }}"
                                        class="calc-trigger w-full rounded-marca-sm border border-linea bg-blanco px-2.5 py-1.5 text-sm font-medium text-azul tabular-nums focus:border-aqua focus:outline-none"
                                    />
                                </div>
                            </div>

                            {{-- Campos ocultos para cálculo dinámico en JS --}}
                            <input type="hidden" id="tmar-{{ $alt->id }}" value="{{ number_format((float) $alt->tasa_tmar, 2, '.', '') }}" />
                            <input type="hidden" id="salv-{{ $alt->id }}" value="{{ number_format((float) $alt->valor_salvamento, 2, '.', '') }}" />
                            <input type="hidden" id="isr-{{ $alt->id }}" value="{{ number_format($tasaIsr, 2, '.', '') }}" />
                        </div>

                        {{-- Los 4 Indicadores Financieros Obligatorios --}}
                        <div class="mt-5 grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                            <div class="rounded-marca-sm border border-amber-200 bg-amber-50 p-3">
                                <span class="block text-xs font-semibold text-tinta-suave">CAUE</span>
                                <strong class="mt-1 block text-base font-semibold text-rose-700 tabular-nums">
                                    $<span id="caue-val-{{ $alt->id }}">{{ number_format((float) $res['caue'], 2, '.', '') }}</span>
                                </strong>
                            </div>

                            <div class="rounded-marca-sm border border-azul-100 bg-azul-100/50 p-3">
                                <span class="block text-xs font-semibold text-tinta-suave">VPN (FEDI)</span>
                                <strong class="mt-1 block text-base font-semibold text-azul tabular-nums">
                                    $<span id="vpn-val-{{ $alt->id }}">{{ number_format((float) $res['vpn'], 2, '.', '') }}</span>
                                </strong>
                            </div>

                            <div class="rounded-marca-sm border border-sky-200 bg-sky-50 p-3">
                                <span class="block text-xs font-semibold text-tinta-suave">TIR</span>
                                <strong class="mt-1 block text-base font-semibold text-azul tabular-nums">
                                    @if ($res['tir'] !== 'N/A')
                                        {{ number_format((float) $res['tir'], 2, '.', '') }}%
                                    @else
                                        N/A
                                    @endif
                                </strong>
                            </div>

                            <div class="rounded-marca-sm border border-aqua/40 bg-aqua-100/60 p-3">
                                <span class="block text-xs font-semibold text-tinta-suave">Relación B/C</span>
                                <strong class="mt-1 block text-base font-semibold text-aqua-600 tabular-nums">
                                    <span id="bc-val-{{ $alt->id }}">{{ number_format((float) $res['bc'], 2, '.', '') }}</span>
                                </strong>
                            </div>
                        </div>

                        {{-- Diagrama de Flujo de Efectivo (Línea de Tiempo con 3 estados: Salida, Neutro, Entrada) --}}
                        <h3 class="mt-6 text-sm font-semibold text-azul">Diagrama de Flujo de Efectivo (Años)</h3>
                        <div
                            id="timeline-container-{{ $alt->id }}"
                            class="mt-2 flex items-center justify-between overflow-x-auto rounded-marca-sm border border-linea bg-neutro/40 p-3"
                        >
                            @foreach ($res['flujo_caja'] as $flujo)
                                <div class="min-w-[115px] px-2 text-center">
                                    @if ($flujo['monto'] < 0)
                                        <span class="mb-1 inline-block rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-700">
                                            ▼ Salida
                                        </span>
                                        <div class="text-sm font-semibold text-rose-700 tabular-nums">
                                            -${{ number_format(abs($flujo['monto']), 2, '.', '') }}
                                        </div>
                                    @elseif ($flujo['monto'] > 0)
                                        <span class="mb-1 inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                            ▲ Entrada
                                        </span>
                                        <div class="text-sm font-semibold text-emerald-700 tabular-nums">
                                            +${{ number_format($flujo['monto'], 2, '.', '') }}
                                        </div>
                                    @else
                                        <span class="mb-1 inline-block rounded-full bg-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                            ● Neutro
                                        </span>
                                        <div class="text-sm font-semibold text-slate-600 tabular-nums">$0.00</div>
                                    @endif

                                    <div class="py-1.5">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-azul text-xs font-semibold text-blanco">
                                            {{ $flujo['anio'] }}
                                        </span>
                                    </div>

                                    <small class="block text-[11px] text-tinta-suave">
                                        @if ($flujo['anio'] === 0)
                                            Inversión Inicial
                                        @elseif ($flujo['anio'] === $alt->vida_util_anios)
                                            Flujo + Rescate
                                        @else
                                            Flujo Neto Anual
                                        @endif
                                    </small>
                                </div>

                                @if (!$loop->last)
                                    <div class="mx-1 h-0.5 min-w-4 flex-1 bg-linea"></div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Tablas de Depreciación (2 Métodos Exigidos) --}}
                        @if (count($res['depreciacion']) > 0)
                            <div class="mt-6">
                                <h3 class="text-xs font-semibold tracking-wider text-tinta-suave uppercase">
                                    Plan de Depreciación (Línea Recta vs. Suma de Dígitos)
                                </h3>
                                <div class="mt-2 overflow-x-auto rounded-marca-sm border border-linea">
                                    <table class="w-full border-collapse text-center text-xs tabular-nums">
                                        <thead class="bg-azul text-blanco">
                                            <tr>
                                                <th class="px-3 py-2 font-medium">Año</th>
                                                <th class="px-3 py-2 font-medium">Cuota L. Recta</th>
                                                <th class="px-3 py-2 font-medium">Saldo L. Recta</th>
                                                <th class="px-3 py-2 font-medium">Cuota Suma Dígitos</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-linea bg-blanco">
                                            @foreach ($res['depreciacion'] as $fila)
                                                @php
                                                    $sdaFila = $res['depreciacion_sda_por_anio']->get($fila['anio']);
                                                @endphp
                                                <tr class="even:bg-neutro/50">
                                                    <td class="px-3 py-2 font-medium text-azul">{{ $fila['anio'] }}</td>
                                                    <td class="px-3 py-2">${{ number_format($fila['cuota'], 2, '.', '') }}</td>
                                                    <td class="px-3 py-2">${{ number_format($fila['saldo'], 2, '.', '') }}</td>
                                                    <td class="px-3 py-2">
                                                        @if ($sdaFila)
                                                            ${{ number_format($sdaFila['cuota'], 2, '.', '') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Trazabilidad de Evidencias Documentales --}}
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-azul">Trazabilidad y Evidencias</h3>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-tinta-suave">
                                @forelse ($res['evidencias'] as $ev)
                                    <li>
                                        <a href="{{ $ev->archivo_url }}" target="_blank" rel="noopener"
                                           class="font-medium text-azul underline decoration-aqua decoration-1 underline-offset-2 hover:text-aqua-600">
                                            {{ $ev->tipo }}: {{ $ev->descripcion }}
                                        </a>
                                    </li>
                                @empty
                                    <li>Sin archivos adjuntos aún.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Botón para Desglosar Toda la Auditoría y Justificación --}}
                        <div class="mt-auto pt-6">
                            <button
                                type="button"
                                data-modal-open="modalDetalle-{{ $alt->id }}"
                                class="w-full cursor-pointer rounded-marca-sm border border-azul px-4 py-2.5 text-sm font-medium text-azul transition hover:bg-azul hover:text-blanco"
                            >
                                Desglosar Justificación, Proveedores y Evidencias
                            </button>
                        </div>
                    </div>
                </article>

                {{-- Modal nativo accesible con el Desglose Completo de Auditoría --}}
                <dialog
                    id="modalDetalle-{{ $alt->id }}"
                    class="m-auto max-h-[90dvh] w-[min(92vw,52rem)] rounded-marca border border-linea bg-blanco p-0 text-azul shadow-2xl backdrop:bg-azul-900/60"
                >
                    <div class="flex max-h-[90dvh] flex-col">
                        <div class="flex items-center justify-between bg-azul px-6 py-4 text-blanco">
                            <h3 class="text-lg font-semibold">Ficha Técnica y Auditoría: {{ $alt->nombre }}</h3>
                            <button
                                type="button"
                                data-modal-close="modalDetalle-{{ $alt->id }}"
                                aria-label="Cerrar"
                                class="cursor-pointer rounded-sm px-2 py-1 text-sm text-azul-100 hover:bg-azul-700 hover:text-blanco"
                            >
                                ✕
                            </button>
                        </div>

                        <div class="space-y-6 overflow-y-auto p-6 text-sm">
                            {{-- 1. Parámetros Oficiales Registrados --}}
                            <section>
                                <h4 class="border-b border-linea pb-1.5 font-semibold text-azul">
                                    1. Parámetros Oficiales Registrados
                                </h4>
                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs sm:grid-cols-3">
                                    <div><strong>Inversión Inicial:</strong> ${{ number_format((float) $alt->inversion_inicial, 2, '.', '') }}</div>
                                    <div><strong>Ingreso / Ahorro Anual:</strong> ${{ number_format((float) $alt->ingreso_anual, 2, '.', '') }}</div>
                                    <div><strong>Costo Operativo Anual:</strong> ${{ number_format((float) $alt->costo_operativo_anual, 2, '.', '') }}</div>
                                    <div><strong>Valor de Salvamento:</strong> ${{ number_format((float) $alt->valor_salvamento, 2, '.', '') }}</div>
                                    <div><strong>Vida Útil:</strong> {{ $alt->vida_util_anios }} años</div>
                                    <div><strong>TMAR Aplicada:</strong> {{ number_format((float) $alt->tasa_tmar, 2, '.', '') }}%</div>
                                </div>
                            </section>

                            {{-- 2. Justificación Operativa y Proveedores --}}
                            <section class="space-y-3">
                                <h4 class="border-b border-linea pb-1.5 font-semibold text-azul">
                                    2. Justificación Operativa y Proveedores
                                </h4>

                                <div>
                                    <p class="font-semibold text-azul">¿Por qué se evaluó esta alternativa?</p>
                                    <p class="mt-0.5 whitespace-pre-line text-tinta-suave">
                                        {{ $alt->justificacion_seleccion ?: 'Sin descripción registrada en el Panel Admin.' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="font-semibold text-azul">Dónde se cotizó / compró el activo (Inversión Inicial):</p>
                                    <p class="mt-0.5 whitespace-pre-line text-tinta-suave">
                                        {{ $alt->detalle_inversion ?: 'No aplica o sin detalle registrado en el Panel Admin.' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="font-semibold text-azul">Desglose de Costos Operativos, Mantenimiento y Seguro:</p>
                                    <p class="mt-0.5 whitespace-pre-line text-tinta-suave">
                                        {{ $alt->detalle_costos_operativos ?: 'Sin detalle operativo registrado en el Panel Admin.' }}
                                    </p>
                                </div>

                                {{-- Tabla de costos por concepto --}}
                                <div class="overflow-x-auto rounded-marca-sm border border-linea">
                                    <table class="w-full border-collapse text-left text-xs">
                                        <thead class="bg-neutro text-azul">
                                            <tr>
                                                <th class="px-3 py-2 font-semibold">Concepto</th>
                                                <th class="px-3 py-2 text-right font-semibold">Monto anual</th>
                                                <th class="px-3 py-2 font-semibold">Fuente</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-linea">
                                            @forelse ($alt->costos as $c)
                                                <tr>
                                                    <td class="px-3 py-2">{{ $c->concepto }}</td>
                                                    <td class="px-3 py-2 text-right tabular-nums">${{ number_format((float) $c->monto_anual, 2, '.', '') }}</td>
                                                    <td class="px-3 py-2">
                                                        @if ($c->fuenteEvidencia)
                                                            <a href="{{ $c->fuenteEvidencia->archivo_url }}" target="_blank" rel="noopener"
                                                               class="text-azul underline decoration-aqua hover:text-aqua-600">
                                                                {{ $c->fuenteEvidencia->descripcion }}
                                                            </a>
                                                        @endif
                                                        @if ($c->fuente_nota)
                                                            <span>{{ $c->fuente_nota }}</span>
                                                        @endif
                                                        @if (!$c->fuenteEvidencia && !$c->fuente_nota)
                                                            <span class="text-rose-700">Sin fuente</span>
                                                        @endif

                                                        @if ($c->fuentes_completas)
                                                            <span class="block font-medium text-emerald-700">✔ {{ $c->num_fuentes }} de 2 fuentes</span>
                                                        @else
                                                            <span class="block font-medium text-rose-700">⚠ {{ $c->num_fuentes }} de 2 fuentes</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-3 py-2 text-tinta-suave">
                                                        Sin costos desglosados en el Panel Admin.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if ($alt->costos->isNotEmpty())
                                            <tfoot class="border-t border-linea bg-neutro/60 font-semibold">
                                                <tr>
                                                    <td class="px-3 py-2">Total desglosado</td>
                                                    <td class="px-3 py-2 text-right tabular-nums">
                                                        ${{ number_format($alt->total_costos_detallados, 2, '.', '') }}
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        @if ($alt->costos_cuadran)
                                                            <span class="text-emerald-700">✔ Coincide con el costo operativo anual</span>
                                                        @else
                                                            <span class="text-rose-700">
                                                                ⚠ No coincide con el costo operativo anual (${{ number_format((float) $alt->costo_operativo_anual, 2, '.', '') }})
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>

                                {{-- Fuentes de los datos clave --}}
                                <h5 class="pt-2 font-semibold text-azul">Fuentes de los datos clave</h5>
                                <div class="overflow-x-auto rounded-marca-sm border border-linea">
                                    <table class="w-full border-collapse text-left text-xs">
                                        <thead class="bg-neutro text-azul">
                                            <tr>
                                                <th class="px-3 py-2 font-semibold">Dato</th>
                                                <th class="px-3 py-2 font-semibold">Valor</th>
                                                <th class="px-3 py-2 font-semibold">Fuentes</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-linea">
                                            @foreach ($alt->fuentes_por_dato as $d)
                                                <tr>
                                                    <td class="px-3 py-2 font-medium">{{ $d['etiqueta'] }}</td>
                                                    <td class="px-3 py-2 tabular-nums">{{ $d['valor'] }}</td>
                                                    <td class="px-3 py-2">
                                                        @foreach ($d['fuentes'] as $f)
                                                            <div>
                                                                @if ($f->fuenteEvidencia)
                                                                    <a href="{{ $f->fuenteEvidencia->archivo_url }}" target="_blank" rel="noopener"
                                                                       class="text-azul underline decoration-aqua hover:text-aqua-600">
                                                                        {{ $f->fuenteEvidencia->descripcion }}
                                                                    </a>
                                                                @endif
                                                                @if ($f->fuente_nota)
                                                                    <span>{{ $f->fuente_nota }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach

                                                        @if ($d['completo'])
                                                            <span class="block font-medium text-emerald-700">✔ {{ $d['cantidad'] }} de 2 fuentes</span>
                                                        @else
                                                            <span class="block font-medium text-rose-700">⚠ {{ $d['cantidad'] }} de 2 fuentes</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            {{-- 3. Fuentes de Verificación y Evidencias Documentales --}}
                            <section class="space-y-3">
                                <h4 class="border-b border-linea pb-1.5 font-semibold text-azul">
                                    3. Fuentes de Verificación y Evidencias Documentales
                                </h4>

                                <div>
                                    <p class="font-semibold text-azul">Respaldo Contable / Entrevistas:</p>
                                    <p class="mt-0.5 whitespace-pre-line text-tinta-suave">
                                        {{ $alt->fuentes_verificacion ?: 'Sin fuentes citadas aún.' }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    @forelse ($res['evidencias'] as $ev)
                                        <a href="{{ $ev->archivo_url }}" target="_blank" rel="noopener"
                                           class="flex items-center justify-between gap-3 rounded-marca-sm border border-linea p-3 transition hover:border-aqua hover:bg-neutro/50">
                                            <div>
                                                <span class="mr-2 inline-block rounded bg-azul px-2 py-0.5 text-[11px] font-medium text-blanco">
                                                    {{ $ev->tipo }}
                                                </span>
                                                <strong class="text-azul">{{ $ev->descripcion }}</strong>
                                            </div>
                                            <span class="shrink-0 rounded-marca-sm bg-aqua px-3 py-1.5 text-xs font-medium text-azul-900">
                                                Abrir Archivo ↗
                                            </span>
                                        </a>
                                    @empty
                                        <div class="rounded-marca-sm border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                                            Aún no se han subido archivos adjuntos (cotizaciones, facturas o entrevistas) para esta alternativa desde el Panel Admin.
                                        </div>
                                    @endforelse
                                </div>
                            </section>
                        </div>

                        <div class="flex items-center justify-between border-t border-linea bg-neutro/50 px-6 py-3">
                            <a href="{{ url('/admin/alternativas/' . $alt->id . '/edit') }}"
                               class="rounded-marca-sm border border-azul/30 bg-blanco px-3 py-1.5 text-xs font-medium text-azul hover:border-azul">
                                ✏️ Editar información en Admin
                            </a>
                            <button
                                type="button"
                                data-modal-close="modalDetalle-{{ $alt->id }}"
                                class="cursor-pointer rounded-marca-sm bg-azul px-4 py-1.5 text-xs font-medium text-blanco hover:bg-azul-700"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </dialog>
            @empty
                <div class="col-span-full rounded-marca border border-linea bg-blanco p-6 text-sm text-tinta-suave">
                    Aún no hay alternativas registradas. Agrégalas desde el panel de administración.
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Control de modales nativos <dialog>
            document.querySelectorAll("[data-modal-open]").forEach((btn) => {
                btn.addEventListener("click", () => {
                    const dialog = document.getElementById(btn.getAttribute("data-modal-open"));
                    if (dialog) dialog.showModal();
                });
            });

            document.querySelectorAll("[data-modal-close]").forEach((btn) => {
                btn.addEventListener("click", () => {
                    const dialog = document.getElementById(btn.getAttribute("data-modal-close"));
                    if (dialog) dialog.close();
                });
            });

            document.querySelectorAll("dialog").forEach((dialog) => {
                dialog.addEventListener("click", (e) => {
                    if (e.target === dialog) dialog.close();
                });
            });

            // Simulador financiero en tiempo real (idéntico al motor original de Django)
            document.querySelectorAll(".calc-trigger").forEach((input) => {
                input.addEventListener("keydown", function (e) {
                    if (e.key === "-" || e.key === "e" || e.key === "E") {
                        e.preventDefault();
                    }
                });

                input.addEventListener("input", function () {
                    const id = this.getAttribute("data-id");
                    recalcularAlternativa(id);
                });
            });

            function recalcularAlternativa(id) {
                const invInput = document.getElementById(`inv-${id}`);
                const ingInput = document.getElementById(`ing-${id}`);
                const costoInput = document.getElementById(`costo-${id}`);
                const vidaInput = document.getElementById(`vida-${id}`);

                let P = parseFloat(invInput.value);
                if (isNaN(P) || P < 0) {
                    P = 0;
                    if (invInput.value !== "") invInput.value = 0;
                }

                let Ing = parseFloat(ingInput.value);
                if (isNaN(Ing) || Ing < 0) {
                    Ing = 0;
                    if (ingInput.value !== "") ingInput.value = 0;
                }

                let A = parseFloat(costoInput.value);
                if (isNaN(A) || A < 0) {
                    A = 0;
                    if (costoInput.value !== "") costoInput.value = 0;
                }

                let n = parseInt(vidaInput.value, 10);
                if (isNaN(n) || n < 1) {
                    n = 1;
                    if (vidaInput.value !== "") vidaInput.value = 1;
                }
                if (n > 50) {
                    n = 50;
                    vidaInput.value = 50;
                }

                const VS = Math.max(0, parseFloat(document.getElementById(`salv-${id}`).value) || 0);
                const i_pct = Math.max(0, parseFloat(document.getElementById(`tmar-${id}`).value) || 12);
                const i = i_pct / 100;
                const tasaISR = Math.max(0, parseFloat(document.getElementById(`isr-${id}`).value) || 30) / 100;

                // 1. Recalcular CAUE
                let caue = 0;
                if (i > 0) {
                    const potencia = Math.pow(1 + i, n);
                    const factorAP = (i * potencia) / (potencia - 1);
                    const factorAF = i / (potencia - 1);
                    caue = P * factorAP + A - VS * factorAF;
                } else {
                    caue = (P - VS) / n + A;
                }
                document.getElementById(`caue-val-${id}`).innerText = caue.toFixed(2);

                // 2. Recalcular VPN (FEDI con ISR) y Relación B/C
                const baseDep = Math.max(P - VS, 0);
                const depAnual = P > 0 ? baseDep / n : 0;
                let vpn = -P;

                for (let t = 1; t <= n; t++) {
                    const uai = Ing - A - depAnual;
                    const impuesto = Math.max(0, uai * tasaISR);
                    let fedi = (Ing - A) - impuesto;
                    if (t === n) fedi += VS;
                    vpn += fedi / Math.pow(1 + i, t);
                }
                if (Math.abs(vpn) < 0.005) vpn = 0;
                document.getElementById(`vpn-val-${id}`).innerText = vpn.toFixed(2);

                let bc = 0;
                if (i > 0) {
                    const potencia = Math.pow(1 + i, n);
                    const factorPA = (potencia - 1) / (i * potencia);
                    const factorPF = 1 / potencia;
                    const vpBeneficios = (Ing * factorPA) + (VS * factorPF);
                    const vpCostos = P + (A * factorPA);
                    bc = vpCostos > 0 ? vpBeneficios / vpCostos : 0;
                }
                document.getElementById(`bc-val-${id}`).innerText = bc.toFixed(2);

                // 3. Reconstruir línea de tiempo con 3 estados: Salida (<0), Neutro (==0), Entrada (>0)
                const timelineContainer = document.getElementById(`timeline-container-${id}`);
                timelineContainer.innerHTML = "";

                for (let anio = 0; anio <= n; anio++) {
                    let monto = 0;
                    let etiqueta = "";

                    if (anio === 0) {
                        monto = -P;
                        etiqueta = "Inversión Inicial";
                    } else if (anio === n) {
                        monto = Ing - A + VS;
                        etiqueta = "Flujo + Rescate";
                    } else {
                        monto = Ing - A;
                        etiqueta = "Flujo Neto Anual";
                    }

                    if (Math.abs(monto) < 0.005) monto = 0;

                    let badgeText = "● Neutro";
                    let badgeClass = "bg-slate-200 text-slate-700";
                    let textClass = "text-slate-600";
                    let montoTexto = "$0.00";

                    if (monto < 0) {
                        badgeText = "▼ Salida";
                        badgeClass = "bg-rose-100 text-rose-700";
                        textClass = "text-rose-700";
                        montoTexto = `-$${Math.abs(monto).toFixed(2)}`;
                    } else if (monto > 0) {
                        badgeText = "▲ Entrada";
                        badgeClass = "bg-emerald-100 text-emerald-700";
                        textClass = "text-emerald-700";
                        montoTexto = `+$${monto.toFixed(2)}`;
                    }

                    const itemDiv = document.createElement("div");
                    itemDiv.className = "min-w-[115px] px-2 text-center";
                    itemDiv.innerHTML = `
                        <span class="mb-1 inline-block rounded-full ${badgeClass} px-2 py-0.5 text-[11px] font-semibold">${badgeText}</span>
                        <div class="text-sm font-semibold ${textClass} tabular-nums">${montoTexto}</div>
                        <div class="py-1.5">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-azul text-xs font-semibold text-blanco">${anio}</span>
                        </div>
                        <small class="block text-[11px] text-tinta-suave">${etiqueta}</small>
                    `;

                    timelineContainer.appendChild(itemDiv);

                    if (anio < n) {
                        const lineDiv = document.createElement("div");
                        lineDiv.className = "mx-1 h-0.5 min-w-4 flex-1 bg-linea";
                        timelineContainer.appendChild(lineDiv);
                    }
                }
            }
        });
    </script>
@endsection