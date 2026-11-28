@extends('layouts.app')

@section('title', '4Seasons | Tablero de evaluación económica')

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">

        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-5 border-b border-linea pb-8">
            <div class="min-w-0 max-w-3xl">
                <h1 class="text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
                    {{ $empresa->nombre ?? '4Seasons' }}: evaluación económica de inversiones
                </h1>
                @if ($empresa?->giro)
                    <p class="mt-2 text-[15px] text-tinta-suave">{{ $empresa->giro }}</p>
                @endif
                <p class="mt-1 text-sm text-tinta-suave">
                    Proyecto Integrador UES-FMO, Sistemas Contables e Ingeniería Económica
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('inicio') }}" class="btn btn-fantasma">
                    <x-icono nombre="flecha-izq" class="size-4" />
                    Inicio
                </a>
                <a href="{{ url('/admin') }}" class="btn btn-secundario">
                    Panel admin
                    <x-icono nombre="flecha-der" class="size-4" />
                </a>
            </div>
        </div>

        @if ($mejor_opcion)
            <section role="status" aria-labelledby="titulo-recomendacion"
                     class="mt-8 rounded-marca-lg bg-azul px-6 py-7 text-blanco sm:px-10 sm:py-9">
                <h2 id="titulo-recomendacion" class="text-[15px] font-medium text-azul-100">Recomendación del sistema</h2>
                <p class="mt-2 max-w-3xl text-lg leading-relaxed text-azul-100">
                    Según el análisis de <strong class="font-semibold text-blanco">Costo Anual Uniforme Equivalente (CAUE)</strong>, la opción más viable económicamente es:
                </p>
                <p class="mt-4 text-3xl leading-tight font-semibold [overflow-wrap:normal] text-aqua hyphens-none sm:text-4xl">
                    {{ $mejor_opcion }}
                </p>
            </section>
        @endif

        <div class="mt-10 space-y-10">
            @forelse ($resultados as $res)
                @php
                    /** @var \App\Models\AlternativaInversion $alt */
                    $alt = $res['objeto'];
                    $tasaIsr = $empresa ? (float) $empresa->tasa_impuesto : 30.00;
                    $esRecomendada = $mejor_opcion && $mejor_opcion === $alt->nombre;
                @endphp

                <article aria-labelledby="titulo-alt-{{ $alt->id }}"
                         @class([
                             'min-w-0 overflow-hidden rounded-marca-lg bg-blanco',
                             'border-2 border-aqua' => $esRecomendada,
                             'border border-linea' => ! $esRecomendada,
                         ])>
                    <header class="bg-azul px-6 py-6 text-blanco sm:px-8">
                        <div class="min-w-0 max-w-3xl">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                <h2 id="titulo-alt-{{ $alt->id }}" class="text-2xl leading-tight font-semibold">{{ $alt->nombre }}</h2>
                                @if ($esRecomendada)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-aqua px-3 py-1 text-sm font-medium text-azul-900">
                                        <x-icono nombre="check" class="size-4" />
                                        Opción recomendada
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-[15px] leading-relaxed text-azul-100">{{ $alt->descripcion }}</p>
                        </div>

                    </header>

                    <div class="space-y-10 p-6 sm:p-8">
                        <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-marca border border-linea bg-linea lg:grid-cols-4">
                            <div class="min-w-0 bg-blanco px-5 py-5">
                                <dt class="text-sm text-tinta-suave">CAUE</dt>
                                <dd class="mt-1 text-2xl font-semibold break-all text-azul tabular-nums sm:text-3xl">
                                    $<span id="caue-val-{{ $alt->id }}">{{ number_format((float) $res['caue'], 2, '.', '') }}</span>
                                </dd>
                            </div>

                            <div class="min-w-0 bg-blanco px-5 py-5">
                                <dt class="text-sm text-tinta-suave">VPN (FEDI)</dt>
                                <dd class="mt-1 text-2xl font-semibold break-all text-azul tabular-nums sm:text-3xl">
                                    $<span id="vpn-val-{{ $alt->id }}">{{ number_format((float) $res['vpn'], 2, '.', '') }}</span>
                                </dd>
                            </div>

                            <div class="min-w-0 bg-blanco px-5 py-5">
                                <dt class="text-sm text-tinta-suave">TIR</dt>
                                <dd class="mt-1 text-2xl font-semibold break-all text-azul tabular-nums sm:text-3xl">
                                    <span id="tir-val-{{ $alt->id }}">@if ($res['tir'] !== 'N/A'){{ number_format((float) $res['tir'], 2, '.', '') }}%@else N/A @endif</span>
                                </dd>
                            </div>

                            <div class="min-w-0 bg-blanco px-5 py-5">
                                <dt class="text-sm text-tinta-suave">Relación B/C</dt>
                                <dd class="mt-1 text-2xl font-semibold break-all text-aqua-600 tabular-nums sm:text-3xl">
                                    <span id="bc-val-{{ $alt->id }}">{{ number_format((float) $res['bc'], 2, '.', '') }}</span>
                                </dd>
                            </div>
                        </dl>

                        @php
                            $defaultsSimulador = [
                                'inversion' => number_format((float) $alt->inversion_inicial, 2, '.', ''),
                                'ingreso' => number_format((float) $alt->ingreso_anual, 2, '.', ''),
                                'costo' => number_format((float) $alt->costo_operativo_anual, 2, '.', ''),
                                'vida' => (int) $alt->vida_util_anios,
                                'varianzas' => collect($res['varianzas'])->mapWithKeys(fn ($v, $anio) => [
                                    $anio => [
                                        'ingreso' => number_format((float) $v['varianza_ingreso_pct'], 2, '.', ''),
                                        'costo' => number_format((float) $v['varianza_costo_pct'], 2, '.', ''),
                                    ],
                                ])->all(),
                            ];
                            $flujosPorAnio = collect($res['flujo_caja'])->keyBy('anio');
                        @endphp

                        <fieldset id="simulador-{{ $alt->id }}"
                                  data-defaults='@json($defaultsSimulador)'
                                  class="min-w-0 rounded-marca bg-neutro p-5 sm:p-6">
                            <legend class="float-left w-full text-base font-semibold text-azul">Simulador de variables</legend>
                            <p class="clear-left pt-1 text-sm text-tinta-suave">Los indicadores y flujos anuales se recalculan en tiempo real (solo en tu navegador, sin alterar la base de datos).</p>

                            <div class="mt-5 grid gap-x-5 gap-y-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div class="min-w-0">
                                    <label for="inv-{{ $alt->id }}" class="etiqueta text-sm whitespace-nowrap">Inversión</label>
                                    <div class="campo-unidad">
                                        <span>$</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            id="inv-{{ $alt->id }}"
                                            value="{{ number_format((float) $alt->inversion_inicial, 2, '.', '') }}"
                                            data-id="{{ $alt->id }}"
                                            class="calc-trigger campo font-medium tabular-nums"
                                        />
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <label for="ing-{{ $alt->id }}" class="etiqueta text-sm whitespace-nowrap">Ingreso o ahorro</label>
                                    <div class="campo-unidad">
                                        <span>$</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            id="ing-{{ $alt->id }}"
                                            value="{{ number_format((float) $alt->ingreso_anual, 2, '.', '') }}"
                                            data-id="{{ $alt->id }}"
                                            class="calc-trigger campo font-medium tabular-nums"
                                        />
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <label for="costo-{{ $alt->id }}" class="etiqueta text-sm whitespace-nowrap">Costo anual</label>
                                    <div class="campo-unidad">
                                        <span>$</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            id="costo-{{ $alt->id }}"
                                            value="{{ number_format((float) $alt->costo_operativo_anual, 2, '.', '') }}"
                                            data-id="{{ $alt->id }}"
                                            class="calc-trigger campo font-medium tabular-nums"
                                        />
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <label for="vida-{{ $alt->id }}" class="etiqueta text-sm whitespace-nowrap">Vida útil</label>
                                    <div class="campo-unidad">
                                        <input
                                            type="number"
                                            min="1"
                                            max="50"
                                            id="vida-{{ $alt->id }}"
                                            value="{{ $alt->vida_util_anios }}"
                                            data-id="{{ $alt->id }}"
                                            class="calc-trigger calc-vida campo font-medium tabular-nums"
                                        />
                                        <span>años</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 border-t border-linea/80 pt-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-azul">Varianzas anuales por año (flujos variables)</h3>
                                        <p class="mt-0.5 text-xs text-tinta-suave">0% = Sin varianza. Modifica la vida útil arriba para agregar o quitar años manteniendo tus ajustes.</p>
                                    </div>
                                    <button type="button"
                                            data-id="{{ $alt->id }}"
                                            class="btn-reset-simulador btn btn-secundario btn-chico">
                                        Restablecer simulación
                                    </button>
                                </div>

                                <div class="mt-3 overflow-x-auto rounded-marca border border-linea bg-blanco">
                                    <table class="w-full min-w-[44rem] border-collapse text-left text-sm">
                                        <thead class="border-b border-linea bg-neutro/60 text-xs text-tinta-suave">
                                            <tr>
                                                <th scope="col" class="w-24 px-3 py-2.5 pl-4 font-medium">Año</th>
                                                <th scope="col" class="w-40 px-3 py-2.5 font-medium">Var. ingreso (%)</th>
                                                <th scope="col" class="w-40 px-3 py-2.5 font-medium">Var. costo (%)</th>
                                                <th scope="col" class="px-3 py-2.5 text-right font-medium">Ingreso año</th>
                                                <th scope="col" class="px-3 py-2.5 text-right font-medium">Costo año</th>
                                                <th scope="col" class="px-3 py-2.5 pr-4 text-right font-medium">Flujo neto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="varianzas-sim-body-{{ $alt->id }}" class="divide-y divide-linea">
                                            @for ($anio = 1; $anio <= $alt->vida_util_anios; $anio++)
                                                @php
                                                    $vAnio = $res['varianzas'][$anio] ?? ['varianza_ingreso_pct' => 0.0, 'varianza_costo_pct' => 0.0];
                                                    $fAnio = $flujosPorAnio->get($anio) ?? ['ingreso' => 0.0, 'costo' => 0.0, 'monto' => 0.0];
                                                    $montoAnio = (float) $fAnio['monto'];
                                                @endphp
                                                <tr data-anio="{{ $anio }}">
                                                    <th scope="row" class="px-3 py-2 pl-4 font-semibold text-azul tabular-nums">
                                                        Año {{ $anio }}
                                                    </th>
                                                    <td class="px-3 py-2">
                                                        <div class="campo-unidad">
                                                            <input type="number"
                                                                   step="0.01"
                                                                   min="-100"
                                                                   max="1000"
                                                                   value="{{ number_format((float) $vAnio['varianza_ingreso_pct'], 2, '.', '') }}"
                                                                   data-id="{{ $alt->id }}"
                                                                   data-anio="{{ $anio }}"
                                                                   data-tipo="ingreso"
                                                                   aria-label="Varianza de ingreso año {{ $anio }} de {{ $alt->nombre }}"
                                                                   class="sim-var-input campo campo-compacto tabular-nums" />
                                                            <span>%</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <div class="campo-unidad">
                                                            <input type="number"
                                                                   step="0.01"
                                                                   min="-100"
                                                                   max="1000"
                                                                   value="{{ number_format((float) $vAnio['varianza_costo_pct'], 2, '.', '') }}"
                                                                   data-id="{{ $alt->id }}"
                                                                   data-anio="{{ $anio }}"
                                                                   data-tipo="costo"
                                                                   aria-label="Varianza de costo año {{ $anio }} de {{ $alt->nombre }}"
                                                                   class="sim-var-input campo campo-compacto tabular-nums" />
                                                            <span>%</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2 text-right tabular-nums sim-ing-anio" data-anio="{{ $anio }}">
                                                        ${{ number_format((float) $fAnio['ingreso'], 2, '.', '') }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right tabular-nums sim-cost-anio" data-anio="{{ $anio }}">
                                                        ${{ number_format((float) $fAnio['costo'], 2, '.', '') }}
                                                    </td>
                                                    <td class="px-3 py-2 pr-4 text-right font-semibold tabular-nums sim-flujo-anio {{ $montoAnio < 0 ? 'text-peligro' : ($montoAnio > 0 ? 'text-exito' : 'text-tinta-suave') }}" data-anio="{{ $anio }}">
                                                        @if ($montoAnio < 0)
                                                            -${{ number_format(abs($montoAnio), 2, '.', '') }}
                                                        @elseif ($montoAnio > 0)
                                                            +${{ number_format($montoAnio, 2, '.', '') }}
                                                        @else
                                                            $0.00
                                                        @endif
                                                        @if ($anio === $alt->vida_util_anios && (float) $alt->valor_salvamento > 0)
                                                            <span class="block text-[11px] font-normal text-tinta-suave">incl. rescate</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <input type="hidden" id="tmar-{{ $alt->id }}" value="{{ number_format((float) $alt->tasa_tmar, 2, '.', '') }}" />
                            <input type="hidden" id="salv-{{ $alt->id }}" value="{{ number_format((float) $alt->valor_salvamento, 2, '.', '') }}" />
                            <input type="hidden" id="isr-{{ $alt->id }}" value="{{ number_format($tasaIsr, 2, '.', '') }}" />
                        </fieldset>

                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-azul">Diagrama de flujo de efectivo (años)</h3>
                            <div
                                id="timeline-container-{{ $alt->id }}"
                                class="mt-4 grid auto-cols-[minmax(8rem,1fr)] grid-flow-col overflow-x-auto overscroll-x-contain pb-3"
                            >
                                @foreach ($res['flujo_caja'] as $flujo)
                                    <div class="text-center">
                                        @if ($flujo['monto'] < 0)
                                            <span class="inline-flex items-center gap-1 text-sm font-medium text-peligro">
                                                <x-icono nombre="flecha-abajo" class="size-4" />
                                                Salida
                                            </span>
                                            <div class="mt-0.5 text-[15px] font-semibold whitespace-nowrap text-peligro tabular-nums">
                                                -${{ number_format(abs($flujo['monto']), 2, '.', '') }}
                                            </div>
                                        @elseif ($flujo['monto'] > 0)
                                            <span class="inline-flex items-center gap-1 text-sm font-medium text-exito">
                                                <x-icono nombre="flecha-arriba" class="size-4" />
                                                Entrada
                                            </span>
                                            <div class="mt-0.5 text-[15px] font-semibold whitespace-nowrap text-exito tabular-nums">
                                                +${{ number_format($flujo['monto'], 2, '.', '') }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-sm font-medium text-tinta-suave">
                                                Neutro
                                            </span>
                                            <div class="mt-0.5 text-[15px] font-semibold whitespace-nowrap text-tinta-suave tabular-nums">$0.00</div>
                                        @endif

                                        <div class="relative py-3">
                                            <span aria-hidden="true" class="absolute inset-x-0 top-1/2 h-px bg-azul/25 {{ $loop->first ? 'left-1/2' : '' }} {{ $loop->last ? 'right-1/2' : '' }}"></span>
                                            <span class="relative inline-flex size-8 items-center justify-center rounded-full bg-azul text-sm font-semibold text-blanco tabular-nums">
                                                {{ $flujo['anio'] }}
                                            </span>
                                        </div>

                                        <small class="block text-sm text-tinta-suave">
                                            @if ($flujo['anio'] === 0)
                                                Inversión inicial
                                            @elseif ($flujo['anio'] === $alt->vida_util_anios)
                                                Flujo + rescate
                                            @else
                                                Flujo neto anual
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-10 border-t border-linea pt-10 lg:grid-cols-12">

                            <div class="min-w-0 lg:col-span-8">
                                @if (count($res['depreciacion']) > 0)
                                    <h3 class="text-base font-semibold text-azul">
                                        Plan de depreciación: línea recta y suma de dígitos
                                    </h3>
                                    <div class="mt-4 overflow-x-auto rounded-marca border border-linea">
                                        <table class="w-full border-collapse text-right text-[15px] tabular-nums">
                                            <thead class="border-b border-linea bg-neutro text-sm text-tinta-suave">
                                                <tr>
                                                    <th scope="col" class="px-5 py-3 text-left font-medium">Año</th>
                                                    <th scope="col" class="px-5 py-3 font-medium">Cuota l. recta</th>
                                                    <th scope="col" class="px-5 py-3 font-medium">Saldo l. recta</th>
                                                    <th scope="col" class="px-5 py-3 font-medium">Cuota suma dígitos</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-linea bg-blanco">
                                                @foreach ($res['depreciacion'] as $fila)
                                                    @php
                                                        $sdaFila = $res['depreciacion_sda_por_anio']->get($fila['anio']);
                                                    @endphp
                                                    <tr>
                                                        <th scope="row" class="px-5 py-3 text-left font-medium text-azul">{{ $fila['anio'] }}</th>
                                                        <td class="px-5 py-3">${{ number_format($fila['cuota'], 2, '.', '') }}</td>
                                                        <td class="px-5 py-3">${{ number_format($fila['saldo'], 2, '.', '') }}</td>
                                                        <td class="px-5 py-3">
                                                            @if ($sdaFila)
                                                                ${{ number_format($sdaFila['cuota'], 2, '.', '') }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <h3 class="text-base font-semibold text-azul">Plan de depreciación</h3>
                                    <p class="mt-3 text-[15px] text-tinta-suave">Sin inversión depreciable para esta alternativa.</p>
                                @endif
                            </div>

                            <div class="min-w-0 lg:col-span-4">
                                <h3 class="text-base font-semibold text-azul">Trazabilidad y evidencias</h3>
                                <ul class="mt-4 space-y-3 text-[15px]">
                                    @forelse ($res['evidencias'] as $ev)
                                        <li class="flex items-start gap-2.5">
                                            <x-icono nombre="archivo" class="mt-0.5 size-4 text-tinta-suave" />
                                            <a href="{{ $ev->archivo_url }}" target="_blank" rel="noopener"
                                               class="min-w-0 font-medium break-words text-azul underline decoration-aqua decoration-1 underline-offset-4 hover:decoration-2">
                                                {{ $ev->tipo }}: {{ $ev->descripcion }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="text-tinta-suave">Sin archivos adjuntos aún.</li>
                                    @endforelse
                                </ul>

                                <button
                                    type="button"
                                    data-modal-open="modalDetalle-{{ $alt->id }}"
                                    class="btn btn-secundario mt-6 w-full justify-between whitespace-normal text-left"
                                >
                                    Desglosar justificación, proveedores y evidencias
                                    <x-icono nombre="flecha-der" class="size-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

                <dialog
                    id="modalDetalle-{{ $alt->id }}"
                    aria-labelledby="titulo-modal-{{ $alt->id }}"
                    class="m-auto max-h-[90dvh] w-[min(92vw,52rem)] rounded-marca-lg bg-blanco p-0 text-azul shadow-2xl backdrop:bg-azul-900/60"
                >
                    <div class="flex max-h-[90dvh] flex-col">
                        <div class="flex items-start justify-between gap-4 border-b border-linea px-6 py-5">
                            <h3 id="titulo-modal-{{ $alt->id }}" class="text-xl font-semibold text-azul">
                                Ficha técnica y auditoría: {{ $alt->nombre }}
                            </h3>
                            <button
                                type="button"
                                data-modal-close="modalDetalle-{{ $alt->id }}"
                                aria-label="Cerrar"
                                class="btn-icono -mt-1 -mr-2 hover:bg-azul-100/60 hover:text-azul"
                            >
                                <x-icono nombre="cerrar" class="size-5" />
                            </button>
                        </div>

                        <div class="space-y-8 overflow-y-auto px-6 py-6 text-[15px]">
                            <section>
                                <h4 class="font-semibold text-azul">Parámetros oficiales registrados</h4>
                                <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3">
                                    <div><dt class="text-sm text-tinta-suave">Inversión inicial</dt><dd class="font-medium tabular-nums">${{ number_format((float) $alt->inversion_inicial, 2, '.', '') }}</dd></div>
                                    <div><dt class="text-sm text-tinta-suave">Ingreso o ahorro anual</dt><dd class="font-medium tabular-nums">${{ number_format((float) $alt->ingreso_anual, 2, '.', '') }}</dd></div>
                                    <div><dt class="text-sm text-tinta-suave">Costo operativo anual</dt><dd class="font-medium tabular-nums">${{ number_format((float) $alt->costo_operativo_anual, 2, '.', '') }}</dd></div>
                                    <div><dt class="text-sm text-tinta-suave">Valor de salvamento</dt><dd class="font-medium tabular-nums">${{ number_format((float) $alt->valor_salvamento, 2, '.', '') }}</dd></div>
                                    <div><dt class="text-sm text-tinta-suave">Vida útil</dt><dd class="font-medium tabular-nums">{{ $alt->vida_util_anios }} años</dd></div>
                                    <div><dt class="text-sm text-tinta-suave">TMAR aplicada</dt><dd class="font-medium tabular-nums">{{ number_format((float) $alt->tasa_tmar, 2, '.', '') }}%</dd></div>
                                </dl>

                                <div class="mt-5 overflow-x-auto rounded-marca border border-linea">
                                    <table class="w-full border-collapse text-left text-sm">
                                        <thead class="border-b border-linea bg-neutro text-tinta-suave">
                                            <tr>
                                                <th scope="col" class="px-4 py-2 font-medium">Año</th>
                                                <th scope="col" class="px-4 py-2 text-right font-medium">Var. ingreso</th>
                                                <th scope="col" class="px-4 py-2 text-right font-medium">Var. costo</th>
                                                <th scope="col" class="px-4 py-2 text-right font-medium">Flujo neto oficial</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-linea">
                                            @for ($anio = 1; $anio <= $alt->vida_util_anios; $anio++)
                                                @php
                                                    $vOf = $res['varianzas'][$anio] ?? ['varianza_ingreso_pct' => 0.0, 'varianza_costo_pct' => 0.0];
                                                    $fOf = $flujosPorAnio->get($anio) ?? ['monto' => 0.0];
                                                @endphp
                                                <tr>
                                                    <td class="px-4 py-2 font-medium text-azul">Año {{ $anio }}</td>
                                                    <td class="px-4 py-2 text-right tabular-nums">{{ number_format((float) $vOf['varianza_ingreso_pct'], 2, '.', '') }}%</td>
                                                    <td class="px-4 py-2 text-right tabular-nums">{{ number_format((float) $vOf['varianza_costo_pct'], 2, '.', '') }}%</td>
                                                    <td class="px-4 py-2 text-right font-medium tabular-nums">${{ number_format((float) $fOf['monto'], 2, '.', '') }}</td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <section class="space-y-4 border-t border-linea pt-6">
                                <h4 class="font-semibold text-azul">Justificación operativa y proveedores</h4>

                                <div>
                                    <p class="font-medium text-azul">¿Por qué se evaluó esta alternativa?</p>
                                    <p class="mt-1 whitespace-pre-line text-tinta-suave">{{ $alt->justificacion_seleccion ?: 'Sin descripción registrada en el Panel Admin.' }}</p>
                                </div>

                                <div>
                                    <p class="font-medium text-azul">Dónde se cotizó o compró el activo (inversión inicial)</p>
                                    <p class="mt-1 whitespace-pre-line text-tinta-suave">{{ $alt->detalle_inversion ?: 'No aplica o sin detalle registrado en el Panel Admin.' }}</p>
                                </div>

                                <div>
                                    <p class="font-medium text-azul">Desglose de costos operativos, mantenimiento y seguro</p>
                                    <p class="mt-1 whitespace-pre-line text-tinta-suave">{{ $alt->detalle_costos_operativos ?: 'Sin detalle operativo registrado en el Panel Admin.' }}</p>
                                </div>

                                <div class="overflow-x-auto rounded-marca border border-linea">
                                    <table class="w-full border-collapse text-left text-sm">
                                        <thead class="border-b border-linea bg-neutro text-tinta-suave">
                                            <tr>
                                                <th scope="col" class="px-4 py-2.5 font-medium">Concepto</th>
                                                <th scope="col" class="px-4 py-2.5 text-right font-medium">Monto anual</th>
                                                <th scope="col" class="px-4 py-2.5 font-medium">Fuente</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-linea">
                                            @forelse ($alt->costos as $c)
                                                <tr class="align-top">
                                                    <td class="px-4 py-2.5">{{ $c->concepto }}</td>
                                                    <td class="px-4 py-2.5 text-right tabular-nums">${{ number_format((float) $c->monto_anual, 2, '.', '') }}</td>
                                                    <td class="px-4 py-2.5">
                                                        @if ($c->fuenteEvidencia)
                                                            <a href="{{ $c->fuenteEvidencia->archivo_url }}" target="_blank" rel="noopener"
                                                               class="text-azul underline decoration-aqua underline-offset-4 hover:decoration-2">
                                                                {{ $c->fuenteEvidencia->descripcion }}
                                                            </a>
                                                        @endif
                                                        @if ($c->fuente_nota)
                                                            <span>{{ $c->fuente_nota }}</span>
                                                        @endif
                                                        @if (!$c->fuenteEvidencia && !$c->fuente_nota)
                                                            <span class="text-peligro">Sin fuente</span>
                                                        @endif

                                                        @if ($c->fuentes_completas)
                                                            <span class="mt-1 flex items-center gap-1 font-medium text-exito">
                                                                <x-icono nombre="check" class="size-4" />
                                                                {{ $c->num_fuentes }} de 2 fuentes
                                                            </span>
                                                        @else
                                                            <span class="mt-1 flex items-center gap-1 font-medium text-peligro">
                                                                <x-icono nombre="alerta" class="size-4" />
                                                                {{ $c->num_fuentes }} de 2 fuentes
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-3 text-tinta-suave">
                                                        Sin costos desglosados en el Panel Admin.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if ($alt->costos->isNotEmpty())
                                            <tfoot class="border-t border-linea bg-neutro/60 font-semibold">
                                                <tr class="align-top">
                                                    <td class="px-4 py-2.5">Total desglosado</td>
                                                    <td class="px-4 py-2.5 text-right tabular-nums">
                                                        ${{ number_format($alt->total_costos_detallados, 2, '.', '') }}
                                                    </td>
                                                    <td class="px-4 py-2.5">
                                                        @if ($alt->costos_cuadran)
                                                            <span class="flex items-center gap-1 text-exito">
                                                                <x-icono nombre="check" class="size-4" />
                                                                Coincide con el costo operativo anual
                                                            </span>
                                                        @else
                                                            <span class="flex items-start gap-1 text-peligro">
                                                                <x-icono nombre="alerta" class="mt-0.5 size-4" />
                                                                No coincide con el costo operativo anual (${{ number_format((float) $alt->costo_operativo_anual, 2, '.', '') }})
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>

                                <h5 class="pt-2 font-medium text-azul">Fuentes de los datos clave</h5>
                                <div class="overflow-x-auto rounded-marca border border-linea">
                                    <table class="w-full border-collapse text-left text-sm">
                                        <thead class="border-b border-linea bg-neutro text-tinta-suave">
                                            <tr>
                                                <th scope="col" class="px-4 py-2.5 font-medium">Dato</th>
                                                <th scope="col" class="px-4 py-2.5 font-medium">Valor</th>
                                                <th scope="col" class="px-4 py-2.5 font-medium">Fuentes</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-linea">
                                            @foreach ($alt->fuentes_por_dato as $d)
                                                <tr class="align-top">
                                                    <td class="px-4 py-2.5 font-medium">{{ $d['etiqueta'] }}</td>
                                                    <td class="px-4 py-2.5 tabular-nums">{{ $d['valor'] }}</td>
                                                    <td class="px-4 py-2.5">
                                                        @foreach ($d['fuentes'] as $f)
                                                            <div>
                                                                @if ($f->fuenteEvidencia)
                                                                    <a href="{{ $f->fuenteEvidencia->archivo_url }}" target="_blank" rel="noopener"
                                                                       class="text-azul underline decoration-aqua underline-offset-4 hover:decoration-2">
                                                                        {{ $f->fuenteEvidencia->descripcion }}
                                                                    </a>
                                                                @endif
                                                                @if ($f->fuente_nota)
                                                                    <span>{{ $f->fuente_nota }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach

                                                        @if ($d['completo'])
                                                            <span class="mt-1 flex items-center gap-1 font-medium text-exito">
                                                                <x-icono nombre="check" class="size-4" />
                                                                {{ $d['cantidad'] }} de 2 fuentes
                                                            </span>
                                                        @else
                                                            <span class="mt-1 flex items-center gap-1 font-medium text-peligro">
                                                                <x-icono nombre="alerta" class="size-4" />
                                                                {{ $d['cantidad'] }} de 2 fuentes
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <section class="space-y-4 border-t border-linea pt-6">
                                <h4 class="font-semibold text-azul">Fuentes de verificación y evidencias documentales</h4>

                                <div>
                                    <p class="font-medium text-azul">Respaldo contable o entrevistas</p>
                                    <p class="mt-1 whitespace-pre-line text-tinta-suave">{{ $alt->fuentes_verificacion ?: 'Sin fuentes citadas aún.' }}</p>
                                </div>

                                <div class="divide-y divide-linea rounded-marca border border-linea">
                                    @forelse ($res['evidencias'] as $ev)
                                        <a href="{{ $ev->archivo_url }}" target="_blank" rel="noopener"
                                           class="flex items-center gap-4 px-4 py-3.5 transition-[background-color] duration-150 first:rounded-t-marca last:rounded-b-marca hover:bg-neutro/70">
                                            <x-icono nombre="archivo" class="size-5 text-tinta-suave" />
                                            <span class="min-w-0 flex-1">
                                                <strong class="block font-medium text-azul">{{ $ev->descripcion }}</strong>
                                                <span class="text-sm text-tinta-suave">{{ $ev->tipo }}</span>
                                            </span>
                                            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-medium text-aqua-600">
                                                Abrir archivo
                                                <x-icono nombre="externo" class="size-4" />
                                            </span>
                                        </a>
                                    @empty
                                        <div class="aviso rounded-marca bg-aviso-100 text-aviso">
                                            <x-icono nombre="alerta" class="mt-0.5 size-5" />
                                            <p>Aún no se han subido archivos adjuntos (cotizaciones, facturas o entrevistas) para esta alternativa desde el Panel Admin.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </section>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-linea bg-neutro/60 px-6 py-4">
                            <a href="{{ url('/admin/alternativas/' . $alt->id . '/edit') }}" class="btn btn-secundario btn-chico">
                                <x-icono nombre="lapiz" class="size-4" />
                                Editar información en Admin
                            </a>
                            <button
                                type="button"
                                data-modal-close="modalDetalle-{{ $alt->id }}"
                                class="btn btn-primario btn-chico px-4"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </dialog>
            @empty
                <div class="col-span-full rounded-marca-lg border border-dashed border-azul/30 p-10 text-center">
                    <p class="text-[15px] text-tinta-suave">Aún no hay alternativas registradas. Agrégalas desde el panel de administración.</p>
                    <a href="{{ url('/admin') }}" class="btn btn-secundario mt-4">
                        Panel admin
                        <x-icono nombre="flecha-der" class="size-4" />
                    </a>
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

            // Simulador financiero en tiempo real (con flujos variables año por año)
            const simVarianzasState = {};

            function redondear2(val) {
                return Math.round((val + Number.EPSILON) * 100) / 100;
            }

            function calcularTirJs(flujos, precision = 0.0001, maxIter = 1000) {
                if (!Array.isArray(flujos) || flujos.length < 2) return null;
                if (flujos[0] >= 0) return null;

                let bajo = -0.999;
                let alto = 5.0;

                const evaluarVpn = (tasa) => {
                    let suma = 0;
                    for (let t = 0; t < flujos.length; t++) {
                        suma += flujos[t] / Math.pow(1 + tasa, t);
                    }
                    return suma;
                };

                let vpnBajo = evaluarVpn(bajo);
                const vpnAlto = evaluarVpn(alto);

                if (!Number.isFinite(vpnBajo) || !Number.isFinite(vpnAlto)) return null;
                if (vpnBajo * vpnAlto > 0) return null;

                let medio = (bajo + alto) / 2;
                for (let iter = 0; iter < maxIter; iter++) {
                    medio = (bajo + alto) / 2;
                    const vpnMedio = evaluarVpn(medio);

                    if (Math.abs(vpnMedio) < precision || (alto - bajo) / 2 < precision) {
                        return redondear2(medio * 100);
                    }

                    if (vpnBajo * vpnMedio < 0) {
                        alto = medio;
                    } else {
                        bajo = medio;
                        vpnBajo = vpnMedio;
                    }
                }

                return redondear2(medio * 100);
            }

            function inicializarEstadoVarianzas(id) {
                if (simVarianzasState[id]) return;
                simVarianzasState[id] = {};
                const tbody = document.getElementById(`varianzas-sim-body-${id}`);
                if (!tbody) return;

                tbody.querySelectorAll("tr[data-anio]").forEach((tr) => {
                    const anio = parseInt(tr.getAttribute("data-anio"), 10);
                    const ingEl = tr.querySelector('input[data-tipo="ingreso"]');
                    const costEl = tr.querySelector('input[data-tipo="costo"]');
                    simVarianzasState[id][anio] = {
                        ingreso: ingEl ? ingEl.value : "0.00",
                        costo: costEl ? costEl.value : "0.00",
                    };
                });
            }

            function renderizarFilasVarianzasSimulador(id, n) {
                inicializarEstadoVarianzas(id);
                const tbody = document.getElementById(`varianzas-sim-body-${id}`);
                if (!tbody) return;

                tbody.innerHTML = "";
                for (let anio = 1; anio <= n; anio++) {
                    if (!simVarianzasState[id][anio]) {
                        simVarianzasState[id][anio] = { ingreso: "0.00", costo: "0.00" };
                    }
                    const { ingreso, costo } = simVarianzasState[id][anio];
                    const tr = document.createElement("tr");
                    tr.setAttribute("data-anio", String(anio));
                    tr.innerHTML = `
                        <th scope="row" class="px-3 py-2 pl-4 font-semibold text-azul tabular-nums">Año ${anio}</th>
                        <td class="px-3 py-2">
                            <div class="campo-unidad">
                                <input type="number" step="0.01" min="-100" max="1000" value="${ingreso}" data-id="${id}" data-anio="${anio}" data-tipo="ingreso" aria-label="Varianza de ingreso año ${anio}" class="sim-var-input campo campo-compacto tabular-nums" />
                                <span>%</span>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <div class="campo-unidad">
                                <input type="number" step="0.01" min="-100" max="1000" value="${costo}" data-id="${id}" data-anio="${anio}" data-tipo="costo" aria-label="Varianza de costo año ${anio}" class="sim-var-input campo campo-compacto tabular-nums" />
                                <span>%</span>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-right tabular-nums sim-ing-anio" data-anio="${anio}">$0.00</td>
                        <td class="px-3 py-2 text-right tabular-nums sim-cost-anio" data-anio="${anio}">$0.00</td>
                        <td class="px-3 py-2 pr-4 text-right font-semibold tabular-nums sim-flujo-anio text-tinta-suave" data-anio="${anio}">$0.00</td>
                    `;
                    tbody.appendChild(tr);
                }
            }

            document.querySelectorAll("fieldset[id^='simulador-']").forEach((fs) => {
                const id = fs.id.replace("simulador-", "");
                inicializarEstadoVarianzas(id);
            });

            document.querySelectorAll(".calc-trigger").forEach((input) => {
                input.addEventListener("keydown", function (e) {
                    if (e.key === "-" || e.key === "e" || e.key === "E") {
                        e.preventDefault();
                    }
                });

                input.addEventListener("input", function () {
                    const id = this.getAttribute("data-id");
                    if (this.classList.contains("calc-vida")) {
                        const raw = this.value.trim();
                        if (raw === "") return;
                        let n = parseInt(raw, 10);
                        if (isNaN(n)) return;
                        if (n < 1) n = 1;
                        if (n > 50) {
                            n = 50;
                            this.value = "50";
                        }
                        renderizarFilasVarianzasSimulador(id, n);
                    }
                    recalcularAlternativa(id);
                });

                if (input.classList.contains("calc-vida")) {
                    input.addEventListener("change", function () {
                        const id = this.getAttribute("data-id");
                        let n = parseInt(this.value, 10);
                        if (isNaN(n) || n < 1) n = 1;
                        if (n > 50) n = 50;
                        this.value = String(n);
                        renderizarFilasVarianzasSimulador(id, n);
                        recalcularAlternativa(id);
                    });
                }
            });

            document.addEventListener("keydown", function (e) {
                if (e.target.classList && e.target.classList.contains("sim-var-input")) {
                    if (e.key === "e" || e.key === "E") {
                        e.preventDefault();
                    }
                }
            });

            document.addEventListener("input", function (e) {
                const varInput = e.target.closest(".sim-var-input");
                if (!varInput) return;

                const id = varInput.getAttribute("data-id");
                const anio = parseInt(varInput.getAttribute("data-anio"), 10);
                const tipo = varInput.getAttribute("data-tipo");

                inicializarEstadoVarianzas(id);
                if (!simVarianzasState[id][anio]) {
                    simVarianzasState[id][anio] = { ingreso: "0.00", costo: "0.00" };
                }

                let val = parseFloat(varInput.value);
                if (!isNaN(val)) {
                    if (val < -100) {
                        val = -100;
                        varInput.value = "-100";
                    } else if (val > 1000) {
                        val = 1000;
                        varInput.value = "1000";
                    }
                }

                simVarianzasState[id][anio][tipo] = varInput.value === "" ? "0.00" : varInput.value;
                recalcularAlternativa(id);
            });

            document.querySelectorAll(".btn-reset-simulador").forEach((btn) => {
                btn.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");
                    const fieldset = document.getElementById(`simulador-${id}`);
                    if (!fieldset) return;

                    const defaults = JSON.parse(fieldset.getAttribute("data-defaults") || "{}");
                    document.getElementById(`inv-${id}`).value = defaults.inversion ?? "0.00";
                    document.getElementById(`ing-${id}`).value = defaults.ingreso ?? "0.00";
                    document.getElementById(`costo-${id}`).value = defaults.costo ?? "0.00";
                    document.getElementById(`vida-${id}`).value = defaults.vida ?? 5;

                    simVarianzasState[id] = {};
                    const vars = defaults.varianzas || {};
                    Object.keys(vars).forEach((anioKey) => {
                        simVarianzasState[id][parseInt(anioKey, 10)] = {
                            ingreso: vars[anioKey].ingreso ?? "0.00",
                            costo: vars[anioKey].costo ?? "0.00",
                        };
                    });

                    renderizarFilasVarianzasSimulador(id, parseInt(defaults.vida ?? 5, 10));
                    recalcularAlternativa(id);
                });
            });

            function recalcularAlternativa(id) {
                inicializarEstadoVarianzas(id);

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

                const salvVal = parseFloat(document.getElementById(`salv-${id}`).value);
                const VS = isNaN(salvVal) ? 0 : Math.max(0, salvVal);

                const tmarVal = parseFloat(document.getElementById(`tmar-${id}`).value);
                const i_pct = isNaN(tmarVal) ? 12 : Math.max(0, tmarVal);
                const i = i_pct / 100;

                const isrVal = parseFloat(document.getElementById(`isr-${id}`).value);
                const tasaISR = (isNaN(isrVal) ? 30 : Math.max(0, isrVal)) / 100;

                // Construir ingresos y costos ajustados año por año (1..n)
                const flujosAnuales = [];
                const tbodySim = document.getElementById(`varianzas-sim-body-${id}`);

                for (let t = 1; t <= n; t++) {
                    const varAnio = simVarianzasState[id][t] || { ingreso: "0", costo: "0" };
                    let vIng = parseFloat(varAnio.ingreso);
                    if (isNaN(vIng)) vIng = 0;
                    vIng = Math.max(-100, Math.min(1000, vIng));

                    let vCost = parseFloat(varAnio.costo);
                    if (isNaN(vCost)) vCost = 0;
                    vCost = Math.max(-100, Math.min(1000, vCost));

                    const ingT = Ing * (1 + vIng / 100);
                    const costT = A * (1 + vCost / 100);
                    const netoT = ingT - costT + (t === n ? VS : 0);

                    flujosAnuales.push({ anio: t, ingT, costT, netoT });

                    if (tbodySim) {
                        const row = tbodySim.querySelector(`tr[data-anio="${t}"]`);
                        if (row) {
                            const ingCell = row.querySelector(".sim-ing-anio");
                            const costCell = row.querySelector(".sim-cost-anio");
                            const flujoCell = row.querySelector(".sim-flujo-anio");

                            if (ingCell) ingCell.textContent = `$${ingT.toFixed(2)}`;
                            if (costCell) costCell.textContent = `$${costT.toFixed(2)}`;
                            if (flujoCell) {
                                let montoMostrar = Math.abs(netoT) < 0.005 ? 0 : netoT;
                                flujoCell.classList.remove("text-peligro", "text-exito", "text-tinta-suave");

                                let textoMonto = "$0.00";
                                if (montoMostrar < 0) {
                                    flujoCell.classList.add("text-peligro");
                                    textoMonto = `-$${Math.abs(montoMostrar).toFixed(2)}`;
                                } else if (montoMostrar > 0) {
                                    flujoCell.classList.add("text-exito");
                                    textoMonto = `+$${montoMostrar.toFixed(2)}`;
                                } else {
                                    flujoCell.classList.add("text-tinta-suave");
                                }

                                const rescateNota = t === n && VS > 0
                                    ? '<span class="block text-[11px] font-normal text-tinta-suave">incl. rescate</span>'
                                    : "";
                                flujoCell.innerHTML = `${textoMonto}${rescateNota}`;
                            }
                        }
                    }
                }

                // 1. Recalcular CAUE mediante flujos irregulares
                let caue = 0;
                if (i > 0) {
                    const potencia = Math.pow(1 + i, n);
                    let vpCostosAnuales = 0;
                    for (const item of flujosAnuales) {
                        vpCostosAnuales += item.costT / Math.pow(1 + i, item.anio);
                    }
                    const vpTotalCostos = P + vpCostosAnuales - VS / potencia;
                    const factorAP = (i * potencia) / (potencia - 1);
                    caue = vpTotalCostos * factorAP;
                } else {
                    const sumaCostos = flujosAnuales.reduce((acc, item) => acc + item.costT, 0);
                    caue = (P + sumaCostos - VS) / n;
                }
                if (Math.abs(caue) < 0.005) caue = 0;
                document.getElementById(`caue-val-${id}`).innerText = caue.toFixed(2);

                // 2. Recalcular FEDI año por año, VPN y TIR
                const baseDep = Math.max(P - VS, 0);
                const depAnual = P > 0 ? redondear2(baseDep / n) : 0;
                const flujosFedi = [-P];
                let vpn = -P;

                for (const item of flujosAnuales) {
                    const uai = item.ingT - item.costT - depAnual;
                    const impuesto = Math.max(0, uai * tasaISR);
                    let fedi = (item.ingT - item.costT) - impuesto;
                    if (item.anio === n) fedi += VS;
                    const fediRedondeado = redondear2(fedi);
                    flujosFedi.push(fediRedondeado);
                    vpn += fediRedondeado / Math.pow(1 + i, item.anio);
                }
                if (Math.abs(vpn) < 0.005) vpn = 0;
                document.getElementById(`vpn-val-${id}`).innerText = vpn.toFixed(2);

                const tir = calcularTirJs(flujosFedi);
                const tirEl = document.getElementById(`tir-val-${id}`);
                if (tirEl) {
                    tirEl.innerText = tir !== null ? `${tir.toFixed(2)}%` : "N/A";
                }

                // 3. Recalcular Relación Beneficio / Costo (flujos irregulares)
                let bc = 0;
                if (i > 0) {
                    const potencia = Math.pow(1 + i, n);
                    let vpIngresos = 0;
                    let vpCostosOp = 0;
                    for (const item of flujosAnuales) {
                        const divisor = Math.pow(1 + i, item.anio);
                        vpIngresos += item.ingT / divisor;
                        vpCostosOp += item.costT / divisor;
                    }
                    const vpBeneficios = vpIngresos + (VS / potencia);
                    const vpCostos = P + vpCostosOp;
                    bc = vpCostos > 0 ? vpBeneficios / vpCostos : 0;
                }
                document.getElementById(`bc-val-${id}`).innerText = bc.toFixed(2);

                // 4. Actualizar línea de tiempo visual (Año 0..n)
                const timelineContainer = document.getElementById(`timeline-container-${id}`);
                timelineContainer.innerHTML = "";

                const svgFlecha = (trazo) =>
                    `<svg class="pointer-events-none size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="${trazo}"/></svg>`;

                for (let anio = 0; anio <= n; anio++) {
                    let monto = 0;
                    let etiqueta = "";

                    if (anio === 0) {
                        monto = -P;
                        etiqueta = "Inversión inicial";
                    } else {
                        monto = flujosAnuales[anio - 1].netoT;
                        etiqueta = anio === n ? "Flujo + rescate" : "Flujo neto anual";
                    }

                    if (Math.abs(monto) < 0.005) monto = 0;

                    let estado = "Neutro";
                    let icono = "";
                    let textClass = "text-tinta-suave";
                    let montoTexto = "$0.00";

                    if (monto < 0) {
                        estado = "Salida";
                        icono = svgFlecha("M12 5v14M18 13l-6 6-6-6");
                        textClass = "text-peligro";
                        montoTexto = `-$${Math.abs(monto).toFixed(2)}`;
                    } else if (monto > 0) {
                        estado = "Entrada";
                        icono = svgFlecha("M12 19V5M6 11l6-6 6 6");
                        textClass = "text-exito";
                        montoTexto = `+$${monto.toFixed(2)}`;
                    }

                    const itemDiv = document.createElement("div");
                    const lineaClase = (anio === 0 ? " left-1/2" : "") + (anio === n ? " right-1/2" : "");

                    itemDiv.className = "text-center";
                    itemDiv.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-sm font-medium ${textClass}">${icono}${estado}</span>
                        <div class="mt-0.5 text-[15px] font-semibold whitespace-nowrap ${textClass} tabular-nums">${montoTexto}</div>
                        <div class="relative py-3">
                            <span aria-hidden="true" class="absolute inset-x-0 top-1/2 h-px bg-azul/25${lineaClase}"></span>
                            <span class="relative inline-flex size-8 items-center justify-center rounded-full bg-azul text-sm font-semibold text-blanco tabular-nums">${anio}</span>
                        </div>
                        <small class="block text-sm text-tinta-suave">${etiqueta}</small>
                    `;

                    timelineContainer.appendChild(itemDiv);
                }
            }
        });
    </script>
@endsection
