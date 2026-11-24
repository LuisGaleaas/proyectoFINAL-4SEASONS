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
                                    @if ($res['tir'] !== 'N/A')
                                        {{ number_format((float) $res['tir'], 2, '.', '') }}%
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>

                            <div class="min-w-0 bg-blanco px-5 py-5">
                                <dt class="text-sm text-tinta-suave">Relación B/C</dt>
                                <dd class="mt-1 text-2xl font-semibold break-all text-aqua-600 tabular-nums sm:text-3xl">
                                    <span id="bc-val-{{ $alt->id }}">{{ number_format((float) $res['bc'], 2, '.', '') }}</span>
                                </dd>
                            </div>
                        </dl>

                        <fieldset class="min-w-0 rounded-marca bg-neutro p-5 sm:p-6">
                            <legend class="float-left w-full text-base font-semibold text-azul">Simulador de variables</legend>
                            <p class="clear-left pt-1 text-sm text-tinta-suave">Los indicadores se recalculan en tiempo real.</p>

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
                                            class="calc-trigger campo font-medium tabular-nums"
                                        />
                                        <span>años</span>
                                    </div>
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

            // Simulador financiero en tiempo real
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

                // Recalcular CAUE
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

                //Recalcular VPN (FEDI con ISR) y Relación B/C
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
                    } else if (anio === n) {
                        monto = Ing - A + VS;
                        etiqueta = "Flujo + rescate";
                    } else {
                        monto = Ing - A;
                        etiqueta = "Flujo neto anual";
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
