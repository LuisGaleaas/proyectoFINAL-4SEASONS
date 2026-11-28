@extends('layouts.app')

@section('title', '4Seasons | Registro de partidas')

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">
        <a href="{{ route('admin.index') }}" class="btn btn-fantasma btn-chico -ml-3">
            <x-icono nombre="flecha-izq" class="size-4" />
            Volver al panel
        </a>

        <div class="mt-4 flex flex-wrap items-end justify-between gap-x-8 gap-y-5 border-b border-linea pb-8">
            <div class="min-w-0 max-w-2xl">
                <h1 class="text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
                    Registro de partidas
                </h1>
                <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                    Asientos contables de 4Seasons con sus cargos y abonos por cuenta.
                </p>
            </div>

            <a href="{{ route('admin.partidas.create') }}" class="btn btn-primario">
                <x-icono nombre="mas" class="size-5" />
                Nueva partida
            </a>
        </div>

        @if (session('status'))
            <div role="status" class="aviso mt-6 bg-exito-100 font-medium text-exito">
                <x-icono nombre="check" class="mt-0.5 size-5" />
                <p>{{ session('status') }}</p>
            </div>
        @endif

        <div class="mt-8 space-y-6">
            @forelse ($partidas as $partida)
                <article aria-labelledby="partida-{{ $partida->id }}" class="overflow-hidden rounded-marca border border-linea bg-blanco">
                    <header class="flex flex-wrap items-start justify-between gap-x-6 gap-y-4 border-b border-linea px-5 py-4">
                        <div class="min-w-0">
                            <h2 id="partida-{{ $partida->id }}" class="text-lg font-semibold text-azul">
                                Partida #{{ $partida->asiento }}
                                <span class="ml-2 text-[15px] font-normal text-tinta-suave tabular-nums">{{ $partida->fecha->format('d/m/Y') }}</span>
                            </h2>
                            <p class="mt-1 text-[15px] leading-relaxed text-azul">{{ $partida->concepto }}</p>
                            <p class="mt-1 text-sm text-tinta-suave">
                                {{ $partida->tipodocumento->value }} · {{ $partida->numerodocumento }}
                                @if ($partida->usuario)
                                    · Registrada por {{ $partida->usuario->name }}
                                @endif
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.partidas.destroy', $partida) }}"
                              onsubmit="return confirm('¿Eliminar la partida #{{ $partida->asiento }} y todos sus movimientos? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-peligro btn-chico">
                                <x-icono nombre="basura" class="size-4" />
                                Eliminar
                            </button>
                        </form>
                    </header>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[40rem] border-collapse text-left text-[15px]">
                            <thead class="border-b border-linea text-sm text-tinta-suave">
                                <tr>
                                    <th scope="col" class="w-28 px-5 py-3 font-medium">Código</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Cuenta</th>
                                    <th scope="col" class="w-40 px-5 py-3 text-right font-medium">Cargo</th>
                                    <th scope="col" class="w-40 px-5 py-3 text-right font-medium">Abono</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-linea">
                                {{-- Primero los cargos y después los abonos, como en el libro diario. --}}
                                @foreach ($partida->detalles->sortBy(fn ($d) => (float) $d->cargo > 0 ? 0 : 1) as $detalle)
                                    <tr>
                                        <td class="px-5 py-3 tabular-nums text-tinta-suave">{{ $detalle->cuenta?->codigo }}</td>
                                        <td class="px-5 py-3 {{ (float) $detalle->cargo > 0 ? '' : 'pl-10' }}">{{ $detalle->cuenta?->cuenta }}</td>
                                        <td class="px-5 py-3 text-right tabular-nums">
                                            {{ (float) $detalle->cargo > 0 ? '$' . number_format((float) $detalle->cargo, 2) : '' }}
                                        </td>
                                        <td class="px-5 py-3 text-right tabular-nums">
                                            {{ (float) $detalle->abono > 0 ? '$' . number_format((float) $detalle->abono, 2) : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-linea bg-neutro/60">
                                <tr>
                                    <th scope="row" colspan="2" class="px-5 py-3 text-right font-medium text-tinta-suave">
                                        @if ($partida->esta_cuadrada)
                                            Totales
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-peligro">
                                                <x-icono nombre="alerta" class="size-4" />
                                                Partida descuadrada
                                            </span>
                                        @endif
                                    </th>
                                    <td class="px-5 py-3 text-right font-semibold text-azul tabular-nums">${{ number_format($partida->total_cargo, 2) }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-azul tabular-nums">${{ number_format($partida->total_abono, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </article>
            @empty
                <div class="rounded-marca border border-dashed border-azul/30 px-5 py-12 text-center">
                    <p class="text-tinta-suave">No hay partidas registradas todavía.</p>
                    <a href="{{ route('admin.partidas.create') }}" class="btn btn-secundario mt-4">
                        <x-icono nombre="mas" class="size-4" />
                        Nueva partida
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
