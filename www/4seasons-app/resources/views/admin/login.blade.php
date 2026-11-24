@extends('layouts.app')

@section('title', '4Seasons | Acceso administrativo')

@section('content')
    <div class="mx-auto grid max-w-6xl gap-10 px-4 pt-10 pb-24 sm:px-6 lg:grid-cols-12 lg:items-start lg:px-8 lg:pt-16">
        <div class="lg:col-span-5 lg:pt-6">
            <h1 class="text-4xl leading-[1.1] font-semibold tracking-[-0.02em] text-azul sm:text-5xl">
                Panel de administración
            </h1>
            <p class="mt-4 max-w-md text-lg leading-relaxed text-tinta-suave">
                Ingresa tus credenciales para gestionar la empresa, las alternativas de inversión y sus respaldos documentales.
            </p>
        </div>

        <div class="min-w-0 lg:col-span-6 lg:col-start-7">
            <form method="POST" action="{{ route('login.post') }}"
                  class="rounded-marca-lg border border-linea bg-blanco p-6 sm:p-9">
                @csrf

                <h2 class="text-2xl font-semibold text-azul">Iniciar sesión</h2>

                @if ($errors->any())
                    <div role="alert" class="aviso mt-5 bg-peligro-100 text-peligro">
                        <x-icono nombre="alerta" class="mt-0.5 size-5" />
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="email" class="etiqueta">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            @error('email') aria-invalid="true" @enderror
                            class="campo"
                        />
                    </div>

                    <div>
                        <label for="password" class="etiqueta">Contraseña</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            @error('password') aria-invalid="true" @enderror
                            class="campo"
                        />
                    </div>

                    <label class="flex cursor-pointer items-center gap-3 text-[15px] text-tinta-suave">
                        <input type="checkbox" name="remember"
                               class="size-5 cursor-pointer rounded-sm border-linea accent-aqua-600" />
                        Mantener sesión iniciada
                    </label>
                </div>

                <button type="submit" class="btn btn-primario mt-8 min-h-12 w-full text-base">
                    Ingresar al panel
                    <x-icono nombre="flecha-der" class="size-5" />
                </button>
            </form>
        </div>
    </div>
@endsection
