@extends('layouts.app')

@section('title', '4Seasons | Cuentas T')

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">
        <a href="{{ route('admin.index') }}" class="btn btn-fantasma btn-chico -ml-3">
            <x-icono nombre="flecha-izq" class="size-4" />
            Volver al panel
        </a>

        <div class="mt-4 flex flex-wrap items-end justify-between gap-x-8 gap-y-5 border-b border-linea pb-8">
            <div class="min-w-0 max-w-2xl">
                <h1 class="text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
                    Cuentas T
                </h1>
                <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                    Catálogo de cuentas contables de 4Seasons ({{ $cuentas->count() }} {{ $cuentas->count() === 1 ? 'cuenta' : 'cuentas' }}).
                </p>
            </div>

            <a href="{{ route('admin.cuentas.create') }}" class="btn btn-primario">
                <x-icono nombre="mas" class="size-5" />
                Nueva cuenta
            </a>
        </div>

        @if (session('status'))
            <div role="status" class="aviso mt-6 bg-exito-100 font-medium text-exito">
                <x-icono nombre="check" class="mt-0.5 size-5" />
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="aviso mt-6 bg-peligro-100 font-medium text-peligro">
                <x-icono nombre="alerta" class="mt-0.5 size-5" />
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="mt-8 overflow-x-auto rounded-marca border border-linea bg-blanco">
            <table class="w-full min-w-[48rem] border-collapse text-left text-[15px]">
                <thead class="border-b border-linea text-sm text-tinta-suave">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 font-medium">Código</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Cuenta</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Tipo</th>
                        <th scope="col" class="px-5 py-3.5 font-medium">Naturaleza</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-medium">Saldo</th>
                        <th scope="col" class="px-5 py-3.5 text-right font-medium"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-linea">
                    @forelse ($cuentas as $cuenta)
                        <tr class="align-top transition-[background-color] duration-150 hover:bg-neutro/60">
                            <td class="px-5 py-4 tabular-nums text-tinta-suave">{{ $cuenta->codigo }}</td>
                            <th scope="row" class="px-5 py-4 font-semibold text-azul">{{ $cuenta->cuenta }}</th>
                            <td class="px-5 py-4 text-tinta-suave">
                                {{ $cuenta->tipocuenta->value }}
                            </td>
                            <td class="px-5 py-4 text-tinta-suave">{{ $cuenta->tiposaldo }}</td>
                            <td class="px-5 py-4 text-right tabular-nums">${{ number_format((float) $cuenta->saldo, 2) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if (Route::has('admin.cuentas.edit'))
                                        <a href="{{ route('admin.cuentas.edit', $cuenta) }}" class="btn btn-secundario btn-chico">
                                            <x-icono nombre="lapiz" class="size-4" />
                                            Editar
                                        </a>
                                    @else
                                        <button type="button" disabled title="Disponible próximamente" class="btn btn-secundario btn-chico">
                                            <x-icono nombre="lapiz" class="size-4" />
                                            Editar
                                        </button>
                                    @endif

                                    @if ($cuenta->detalles_count > 0)
                                        <button type="button" disabled
                                                title="Tiene {{ $cuenta->detalles_count }} movimientos en partidas"
                                                class="btn btn-peligro btn-chico">
                                            <x-icono nombre="basura" class="size-4" />
                                            Eliminar
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('admin.cuentas.destroy', $cuenta) }}"
                                              onsubmit="return confirm('¿Eliminar la cuenta {{ $cuenta->codigo }}? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-peligro btn-chico">
                                                <x-icono nombre="basura" class="size-4" />
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="text-tinta-suave">No hay cuentas registradas todavía.</p>
                                <a href="{{ route('admin.cuentas.create') }}" class="btn btn-secundario mt-4">
                                    <x-icono nombre="mas" class="size-4" />
                                    Nueva cuenta
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
