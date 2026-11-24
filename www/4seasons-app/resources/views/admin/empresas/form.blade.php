@extends('layouts.app')

@section('title', '4Seasons | ' . ($empresa->exists ? 'Editar empresa' : 'Registrar empresa'))

@section('content')
    <div class="mx-auto max-w-4xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">
        <a href="{{ route('admin.index') }}" class="btn btn-fantasma btn-chico -ml-3">
            <x-icono nombre="flecha-izq" class="size-4" />
            Volver al panel
        </a>

        <h1 class="mt-4 text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
            {{ $empresa->exists ? 'Editar empresa' : 'Registrar empresa' }}
        </h1>

        @if ($errors->any())
            <div role="alert" class="aviso mt-6 bg-peligro-100 text-peligro">
                <x-icono nombre="alerta" class="mt-0.5 size-5" />
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $empresa->exists ? route('admin.empresas.update', $empresa) : route('admin.empresas.store') }}"
              class="mt-8 rounded-marca-lg border border-linea bg-blanco">
            @csrf
            @if ($empresa->exists)
                @method('PUT')
            @endif

            <fieldset class="grid gap-x-5 gap-y-6 p-6 sm:grid-cols-2 sm:p-8">
                <legend class="sr-only">Datos de la empresa</legend>

                <div>
                    <label for="nombre" class="etiqueta">Nombre de la empresa</label>
                    <input type="text" id="nombre" name="nombre" maxlength="150" required
                           value="{{ old('nombre', $empresa->nombre ?? '4Seasons') }}"
                           @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
                           class="campo" />
                    @error('nombre') <p id="nombre-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tasa_impuesto" class="etiqueta">Tasa ISR</label>
                    <div class="campo-unidad">
                        <input type="number" id="tasa_impuesto" name="tasa_impuesto" step="0.01" min="0" max="100" required
                               value="{{ old('tasa_impuesto', $empresa->tasa_impuesto ?? '30.00') }}"
                               @error('tasa_impuesto') aria-invalid="true" aria-describedby="tasa_impuesto-error" @enderror
                               class="campo tabular-nums" />
                        <span>%</span>
                    </div>
                    @error('tasa_impuesto') <p id="tasa_impuesto-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="giro" class="etiqueta">Giro comercial</label>
                    <input type="text" id="giro" name="giro" maxlength="255" required
                           value="{{ old('giro', $empresa->giro ?? 'Comercialización de prendas de vestir y accesorios') }}"
                           @error('giro') aria-invalid="true" aria-describedby="giro-error" @enderror
                           class="campo" />
                    @error('giro') <p id="giro-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-linea pt-6 sm:col-span-2 sm:grid sm:grid-cols-2 sm:gap-x-5">
                    <div>
                        <label for="propietario_contacto" class="etiqueta">Propietario o contacto</label>
                        <input type="text" id="propietario_contacto" name="propietario_contacto" maxlength="150" required
                               autocomplete="name"
                               value="{{ old('propietario_contacto', $empresa->propietario_contacto) }}"
                               @error('propietario_contacto') aria-invalid="true" aria-describedby="propietario_contacto-error" @enderror
                               class="campo" />
                        @error('propietario_contacto') <p id="propietario_contacto-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6 sm:mt-0">
                        <label for="telefono" class="etiqueta">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" maxlength="20" required
                               inputmode="tel" autocomplete="tel"
                               value="{{ old('telefono', $empresa->telefono) }}"
                               @error('telefono') aria-invalid="true" aria-describedby="telefono-error" @enderror
                               class="campo tabular-nums" />
                        @error('telefono') <p id="telefono-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <div class="flex flex-col-reverse gap-3 rounded-b-marca-lg border-t border-linea bg-neutro/60 px-6 py-4 sm:flex-row sm:justify-end sm:px-8">
                <a href="{{ route('admin.index') }}" class="btn btn-secundario">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <x-icono nombre="check" class="size-5" />
                    Guardar empresa
                </button>
            </div>
        </form>
    </div>
@endsection
