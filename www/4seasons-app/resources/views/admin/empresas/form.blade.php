@extends('layouts.app')

@section('title', '4Seasons | ' . ($empresa->exists ? 'Editar Empresa' : 'Registrar Empresa'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 pt-6 pb-20 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between border-b border-linea pb-4">
            <h1 class="text-2xl font-semibold text-azul">
                {{ $empresa->exists ? 'Editar Empresa' : 'Registrar Empresa' }}
            </h1>
            <a href="{{ route('admin.index') }}" class="text-sm font-medium text-tinta-suave hover:text-azul">
                ← Volver al panel
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-marca-sm border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $empresa->exists ? route('admin.empresas.update', $empresa) : route('admin.empresas.store') }}"
              class="space-y-5 rounded-marca border border-linea bg-blanco p-6 shadow-xs">
            @csrf
            @if ($empresa->exists)
                @method('PUT')
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nombre" class="mb-1 block text-sm font-medium text-azul">Nombre de la empresa</label>
                    <input type="text" id="nombre" name="nombre" maxlength="150" required
                           value="{{ old('nombre', $empresa->nombre ?? '4Seasons') }}"
                           class="w-full rounded-marca-sm border border-linea px-3.5 py-2 text-sm text-azul focus:border-aqua focus:outline-none" />
                </div>

                <div>
                    <label for="tasa_impuesto" class="mb-1 block text-sm font-medium text-azul">Tasa ISR (%)</label>
                    <input type="number" id="tasa_impuesto" name="tasa_impuesto" step="0.01" min="0" max="100" required
                           value="{{ old('tasa_impuesto', $empresa->tasa_impuesto ?? '30.00') }}"
                           class="w-full rounded-marca-sm border border-linea px-3.5 py-2 text-sm text-azul tabular-nums focus:border-aqua focus:outline-none" />
                </div>
            </div>

            <div>
                <label for="giro" class="mb-1 block text-sm font-medium text-azul">Giro comercial</label>
                <input type="text" id="giro" name="giro" maxlength="255" required
                       value="{{ old('giro', $empresa->giro ?? 'Comercialización de prendas de vestir y accesorios') }}"
                       class="w-full rounded-marca-sm border border-linea px-3.5 py-2 text-sm text-azul focus:border-aqua focus:outline-none" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="propietario_contacto" class="mb-1 block text-sm font-medium text-azul">Propietario / Contacto</label>
                    <input type="text" id="propietario_contacto" name="propietario_contacto" maxlength="150" required
                           value="{{ old('propietario_contacto', $empresa->propietario_contacto) }}"
                           class="w-full rounded-marca-sm border border-linea px-3.5 py-2 text-sm text-azul focus:border-aqua focus:outline-none" />
                </div>

                <div>
                    <label for="telefono" class="mb-1 block text-sm font-medium text-azul">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" maxlength="20" required
                           value="{{ old('telefono', $empresa->telefono) }}"
                           class="w-full rounded-marca-sm border border-linea px-3.5 py-2 text-sm text-azul focus:border-aqua focus:outline-none" />
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-linea pt-4">
                <a href="{{ route('admin.index') }}"
                   class="rounded-marca-sm border border-linea px-4 py-2 text-sm font-medium text-tinta-suave hover:text-azul">
                    Cancelar
                </a>
                <button type="submit"
                        class="cursor-pointer rounded-marca-sm bg-azul px-5 py-2 text-sm font-medium text-blanco hover:bg-azul-700">
                    Guardar Empresa
                </button>
            </div>
        </form>
    </div>
@endsection