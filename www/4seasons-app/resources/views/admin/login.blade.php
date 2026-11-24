@extends('layouts.app')

@section('title', '4Seasons | Acceso Administrativo')

@section('content')
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-marca border border-linea bg-blanco p-8 shadow-xs">
            <p class="text-xs font-semibold tracking-wider text-aqua-600 uppercase">Panel de Administración</p>
            <h1 class="mt-1 text-2xl font-semibold text-azul">Iniciar sesión</h1>
            <p class="mt-2 text-sm text-tinta-suave">
                Ingresa tus credenciales para gestionar la empresa, las alternativas de inversión y sus respaldos documentales.
            </p>

            @if ($errors->any())
                <div class="mt-5 rounded-marca-sm border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-azul">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-marca-sm border border-linea bg-blanco px-3.5 py-2.5 text-sm text-azul focus:border-aqua focus:outline-none"
                    />
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-azul">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full rounded-marca-sm border border-linea bg-blanco px-3.5 py-2.5 text-sm text-azul focus:border-aqua focus:outline-none"
                    />
                </div>

                <label class="flex items-center gap-2 text-xs text-tinta-suave">
                    <input type="checkbox" name="remember" class="rounded border-linea text-aqua focus:ring-aqua" />
                    Mantener sesión iniciada
                </label>

                <button
                    type="submit"
                    class="w-full cursor-pointer rounded-marca-sm bg-azul px-5 py-2.5 text-sm font-medium text-blanco transition hover:bg-azul-700"
                >
                    Ingresar al panel
                </button>
            </form>
        </div>
    </div>
@endsection