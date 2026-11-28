@extends('layouts.app')

@section('title', '4Seasons | ' . ($cuenta->exists ? 'Editar cuenta' : 'Nueva cuenta'))

@section('content')
    <div class="mx-auto max-w-4xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">
        <a href="{{ route('admin.cuentas.index') }}" class="btn btn-fantasma btn-chico -ml-3">
            <x-icono nombre="flecha-izq" class="size-4" />
            Volver a cuentas
        </a>

        <h1 class="mt-4 text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
            {{ $cuenta->exists ? 'Editar cuenta' : 'Nueva cuenta' }}
        </h1>
        <p class="mt-2 text-[15px] text-tinta-suave">
            Registra una cuenta del catálogo contable para usarla en las partidas y en su mayorización (cuenta T).
        </p>

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
              action="{{ $cuenta->exists ? route('admin.cuentas.update', $cuenta) : route('admin.cuentas.store') }}"
              class="mt-8 rounded-marca-lg border border-linea bg-blanco">
            @csrf
            @if ($cuenta->exists)
                @method('PUT')
            @endif

            <fieldset class="grid gap-x-5 gap-y-6 p-6 sm:grid-cols-2 sm:p-8">
                <legend class="sr-only">Datos de la cuenta</legend>

                <div>
                    <label for="codigo" class="etiqueta">Código</label>
                    <input type="text" id="codigo" name="codigo" maxlength="50" required
                           placeholder="1101"
                           value="{{ old('codigo', $cuenta->codigo) }}"
                           aria-describedby="codigo-ayuda @error('codigo') codigo-error @enderror"
                           @error('codigo') aria-invalid="true" @enderror
                           class="campo tabular-nums" />
                    <p id="codigo-ayuda" class="ayuda">Debe ser único dentro del catálogo.</p>
                    @error('codigo') <p id="codigo-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="saldo" class="etiqueta">Saldo inicial</label>
                    <div class="campo-unidad">
                        <span>$</span>
                        <input type="number" id="saldo" name="saldo" step="0.01" min="0" required
                               value="{{ old('saldo', $cuenta->saldo ?? '0.00') }}"
                               @error('saldo') aria-invalid="true" aria-describedby="saldo-error" @enderror
                               class="campo tabular-nums" />
                    </div>
                    @error('saldo') <p id="saldo-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="cuenta" class="etiqueta">Nombre de la cuenta</label>
                    <input type="text" id="cuenta" name="cuenta" maxlength="150" required
                           placeholder="Efectivo y Equivalentes (Caja y Bancos)"
                           value="{{ old('cuenta', $cuenta->cuenta) }}"
                           @error('cuenta') aria-invalid="true" aria-describedby="cuenta-error" @enderror
                           class="campo" />
                    @error('cuenta') <p id="cuenta-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-linea pt-6 sm:col-span-2 sm:grid sm:grid-cols-2 sm:gap-x-5">
                    <div>
                        <label for="tipocuenta" class="etiqueta">Tipo de cuenta</label>
                        <select id="tipocuenta" name="tipocuenta" required
                                @error('tipocuenta') aria-invalid="true" aria-describedby="tipocuenta-error" @enderror
                                class="campo">
                            <option value="" disabled @selected(! old('tipocuenta', $cuenta->tipocuenta))>Selecciona un tipo</option>
                            @foreach (\App\Models\Cuenta::TIPOS_CUENTA as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tipocuenta', $cuenta->tipocuenta) === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipocuenta') <p id="tipocuenta-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6 sm:mt-0">
                        <label for="tiposaldo" class="etiqueta">Naturaleza del saldo</label>
                        <select id="tiposaldo" name="tiposaldo" required
                                aria-describedby="tiposaldo-ayuda @error('tiposaldo') tiposaldo-error @enderror"
                                @error('tiposaldo') aria-invalid="true" @enderror
                                class="campo">
                            <option value="" disabled @selected(! old('tiposaldo', $cuenta->tiposaldo))>Selecciona la naturaleza</option>
                            @foreach (\App\Models\Cuenta::TIPOS_SALDO as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tiposaldo', $cuenta->tiposaldo) === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        <p id="tiposaldo-ayuda" class="ayuda">Activo, Costos y Gastos suelen ser deudoras; Pasivo, Patrimonio e Ingresos, acreedoras.</p>
                        @error('tiposaldo') <p id="tiposaldo-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <div class="flex flex-col-reverse gap-3 rounded-b-marca-lg border-t border-linea bg-neutro/60 px-6 py-4 sm:flex-row sm:justify-end sm:px-8">
                <a href="{{ route('admin.cuentas.index') }}" class="btn btn-secundario">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primario">
                    <x-icono nombre="check" class="size-5" />
                    Guardar cuenta
                </button>
            </div>
        </form>
    </div>
@endsection
