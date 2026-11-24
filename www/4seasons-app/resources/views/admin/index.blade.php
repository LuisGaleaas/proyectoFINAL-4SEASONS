@extends('layouts.app')

@section('title', '4Seasons | Panel Administrativo')

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-20 sm:px-6 lg:px-8">
        {{-- Cabecera --}}
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-linea pb-6">
            <div>
                <p class="text-sm font-medium text-aqua-600">Administración del Sistema</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-[-0.02em] text-azul">
                    Gestión de Datos y Auditoría
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex min-h-10 items-center rounded-marca-sm border border-azul/25 bg-blanco px-4 text-sm font-medium text-azul hover:border-azul">
                    Ver Tablero
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex min-h-10 cursor-pointer items-center rounded-marca-sm border border-rose-300 bg-blanco px-4 text-sm font-medium text-rose-700 hover:bg-rose-50">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div role="status" class="mb-6 rounded-marca-sm border border-aqua bg-aqua-100/80 px-4 py-3 text-sm font-medium text-azul-900">
                {{ session('status') }}
            </div>
        @endif

        {{-- Bloque 1: Empresa --}}
        <section class="mb-10 rounded-marca border border-linea bg-blanco p-6 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-azul">1. Configuración de la Empresa</h2>
                    <p class="text-sm text-tinta-suave">Datos generales y tasa de Impuesto Sobre la Renta (ISR) aplicada a los cálculos FEDI.</p>
                </div>

                @if ($empresa)
                    <a href="{{ route('admin.empresas.edit', $empresa) }}"
                       class="inline-flex min-h-10 items-center rounded-marca-sm bg-azul px-4 text-sm font-medium text-blanco hover:bg-azul-700">
                        Editar Empresa
                    </a>
                @else
                    <a href="{{ route('admin.empresas.create') }}"
                       class="inline-flex min-h-10 items-center rounded-marca-sm bg-aqua px-4 text-sm font-medium text-azul-900 hover:bg-aqua-600">
                        + Registrar Empresa
                    </a>
                @endif
            </div>

            @if ($empresa)
                <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-linea pt-4 text-sm sm:grid-cols-4">
                    <div>
                        <dt class="text-xs text-tinta-suave">Nombre</dt>
                        <dd class="font-semibold text-azul">{{ $empresa->nombre }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-tinta-suave">Propietario / Contacto</dt>
                        <dd class="font-medium text-azul">{{ $empresa->propietario_contacto }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-tinta-suave">Teléfono</dt>
                        <dd class="font-medium text-azul">{{ $empresa->telefono }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-tinta-suave">Tasa ISR</dt>
                        <dd class="font-semibold text-aqua-600 tabular-nums">{{ number_format((float) $empresa->tasa_impuesto, 2) }}%</dd>
                    </div>
                </dl>
            @endif
        </section>

        {{-- Bloque 2: Alternativas de Inversión --}}
        <section class="rounded-marca border border-linea bg-blanco p-6 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-azul">2. Alternativas de Inversión</h2>
                    <p class="text-sm text-tinta-suave">
                        Gestiona parámetros financieros, desglose de costos por concepto, fuentes de datos clave y evidencias ({{ $totalEvidencias }} archivos en total).
                    </p>
                </div>

                <a href="{{ route('admin.alternativas.create') }}"
                   class="inline-flex min-h-10 items-center rounded-marca-sm bg-aqua px-4 text-sm font-medium text-azul-900 hover:bg-aqua-600">
                    + Nueva Alternativa
                </a>
            </div>

            <div class="mt-6 overflow-x-auto rounded-marca-sm border border-linea">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-azul text-blanco">
                        <tr>
                            <th class="px-4 py-3 font-medium">Alternativa</th>
                            <th class="px-4 py-3 text-right font-medium">Inversión</th>
                            <th class="px-4 py-3 text-right font-medium">Ingreso/Ahorro</th>
                            <th class="px-4 py-3 text-right font-medium">Costo Anual</th>
                            <th class="px-4 py-3 text-center font-medium">Vida / TMAR</th>
                            <th class="px-4 py-3 text-center font-medium">Auditoría</th>
                            <th class="px-4 py-3 text-right font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-linea">
                        @forelse ($alternativas as $alt)
                            <tr class="even:bg-neutro/40">
                                <td class="px-4 py-3 font-semibold text-azul">{{ $alt->nombre }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">${{ number_format((float) $alt->inversion_inicial, 2) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">${{ number_format((float) $alt->ingreso_anual, 2) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">${{ number_format((float) $alt->costo_operativo_anual, 2) }}</td>
                                <td class="px-4 py-3 text-center tabular-nums">
                                    {{ $alt->vida_util_anios }} años · {{ number_format((float) $alt->tasa_tmar, 2) }}%
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-tinta-suave">
                                    {{ $alt->costos_count }} costos · {{ $alt->fuentes_datos_count }} fuentes · {{ $alt->evidencias_count }} archivos
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.alternativas.edit', $alt) }}"
                                           class="rounded-marca-sm border border-azul/30 px-3 py-1.5 text-xs font-medium text-azul hover:border-azul hover:bg-azul hover:text-blanco">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.alternativas.destroy', $alt) }}"
                                              onsubmit="return confirm('¿Eliminar esta alternativa y todos sus costos, fuentes y evidencias?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="cursor-pointer rounded-marca-sm border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-tinta-suave">
                                    No hay alternativas de inversión registradas todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
