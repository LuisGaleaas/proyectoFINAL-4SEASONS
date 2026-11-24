@extends('layouts.app')

@section('title', '4Seasons | Panel administrativo')

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">
        
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-5 border-b border-linea pb-8">
            <div class="min-w-0">
                <h1 class="text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
                    Gestión de datos y auditoría
                </h1>
                <p class="mt-2 text-[15px] text-tinta-suave">Administración del sistema</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-secundario">
                    Ver tablero
                    <x-icono nombre="flecha-der" class="size-4" />
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-fantasma hover:bg-peligro-100 hover:text-peligro">
                        <x-icono nombre="salir" class="size-4" />
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div role="status" class="aviso mt-6 bg-exito-100 font-medium text-exito">
                <x-icono nombre="check" class="mt-0.5 size-5" />
                <p>{{ session('status') }}</p>
            </div>
        @endif


        <section aria-labelledby="titulo-empresa" class="grid gap-6 border-b border-linea py-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <h2 id="titulo-empresa" class="text-xl font-semibold text-azul">Configuración de la empresa</h2>
                <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                    Datos generales y tasa de Impuesto Sobre la Renta (ISR) aplicada a los cálculos FEDI.
                </p>
                <div class="mt-5">
                    @if ($empresa)
                        <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-secundario">
                            <x-icono nombre="lapiz" class="size-4" />
                            Editar empresa
                        </a>
                    @else
                        <a href="{{ route('admin.empresas.create') }}" class="btn btn-primario">
                            <x-icono nombre="mas" class="size-5" />
                            Registrar empresa
                        </a>
                    @endif
                </div>
            </div>

            <div class="min-w-0 lg:col-span-8">
                @if ($empresa)
                    <dl class="grid gap-px overflow-hidden rounded-marca border border-linea bg-linea sm:grid-cols-2">
                        <div class="bg-blanco p-5">
                            <dt class="text-sm text-tinta-suave">Nombre</dt>
                            <dd class="mt-1 text-lg font-semibold text-azul">{{ $empresa->nombre }}</dd>
                        </div>
                        <div class="bg-blanco p-5">
                            <dt class="text-sm text-tinta-suave">Tasa ISR</dt>
                            <dd class="mt-1 text-lg font-semibold text-azul tabular-nums">{{ number_format((float) $empresa->tasa_impuesto, 2) }}%</dd>
                        </div>
                        <div class="bg-blanco p-5">
                            <dt class="text-sm text-tinta-suave">Propietario o contacto</dt>
                            <dd class="mt-1 font-medium text-azul">{{ $empresa->propietario_contacto }}</dd>
                        </div>
                        <div class="bg-blanco p-5">
                            <dt class="text-sm text-tinta-suave">Teléfono</dt>
                            <dd class="mt-1 font-medium text-azul tabular-nums">{{ $empresa->telefono }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="rounded-marca border border-dashed border-azul/30 p-6 text-[15px] text-tinta-suave">
                        Todavía no hay una empresa registrada.
                    </p>
                @endif
            </div>
        </section>


        <section aria-labelledby="titulo-alternativas" class="pt-10">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="max-w-2xl">
                    <h2 id="titulo-alternativas" class="text-xl font-semibold text-azul">Alternativas de inversión</h2>
                    <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                        Gestiona parámetros financieros, desglose de costos por concepto, fuentes de datos clave y evidencias ({{ $totalEvidencias }} archivos en total).
                    </p>
                </div>

                <a href="{{ route('admin.alternativas.create') }}" class="btn btn-primario">
                    <x-icono nombre="mas" class="size-5" />
                    Nueva alternativa
                </a>
            </div>

            <div class="mt-6 overflow-x-auto rounded-marca border border-linea bg-blanco">
                <table class="w-full min-w-[56rem] border-collapse text-left text-[15px]">
                    <thead class="border-b border-linea text-sm text-tinta-suave">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 font-medium">Alternativa</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-medium">Inversión</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-medium">Ingreso o ahorro</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-medium">Costo anual</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-medium">Vida útil</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-medium">TMAR</th>
                            <th scope="col" class="px-5 py-3.5 font-medium">Auditoría</th>
                            <th scope="col" class="px-5 py-3.5 text-right font-medium"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-linea">
                        @forelse ($alternativas as $alt)
                            <tr class="align-top transition-[background-color] duration-150 hover:bg-neutro/60">
                                <th scope="row" class="px-5 py-4 font-semibold text-azul">{{ $alt->nombre }}</th>
                                <td class="px-5 py-4 text-right tabular-nums">${{ number_format((float) $alt->inversion_inicial, 2) }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">${{ number_format((float) $alt->ingreso_anual, 2) }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">${{ number_format((float) $alt->costo_operativo_anual, 2) }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ $alt->vida_util_anios }} años</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ number_format((float) $alt->tasa_tmar, 2) }}%</td>
                                <td class="px-5 py-4 text-sm text-tinta-suave">
                                    <ul class="space-y-0.5 tabular-nums">
                                        <li>{{ $alt->costos_count }} costos</li>
                                        <li>{{ $alt->fuentes_datos_count }} fuentes</li>
                                        <li>{{ $alt->evidencias_count }} archivos</li>
                                    </ul>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.alternativas.edit', $alt) }}" class="btn btn-secundario btn-chico">
                                            <x-icono nombre="lapiz" class="size-4" />
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.alternativas.destroy', $alt) }}"
                                              onsubmit="return confirm('¿Eliminar esta alternativa y todos sus costos, fuentes y evidencias?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-peligro btn-chico">
                                                <x-icono nombre="basura" class="size-4" />
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <p class="text-tinta-suave">No hay alternativas de inversión registradas todavía.</p>
                                    <a href="{{ route('admin.alternativas.create') }}" class="btn btn-secundario mt-4">
                                        <x-icono nombre="mas" class="size-4" />
                                        Nueva alternativa
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
