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
    </div>
@endsection
