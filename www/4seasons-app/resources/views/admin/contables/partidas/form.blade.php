@extends('layouts.app')

@section('title', '4Seasons | Nueva partida')

@section('content')
    @php
        $movimientos = old('detalles') ?: [[], []];
        $tipoActual = old('tipodocumento', $partida->tipodocumento?->value);
    @endphp

    <div class="mx-auto max-w-5xl px-4 pt-6 pb-24 sm:px-6 lg:px-8">
        <a href="{{ route('admin.partidas.index') }}" class="btn btn-fantasma btn-chico -ml-3">
            <x-icono nombre="flecha-izq" class="size-4" />
            Volver a partidas
        </a>

        <h1 class="mt-4 text-3xl leading-tight font-semibold tracking-[-0.02em] text-azul sm:text-4xl">
            Nueva partida
        </h1>
        <p class="mt-2 text-[15px] text-tinta-suave">
            Registra un asiento contable. La suma de los cargos debe ser igual a la suma de los abonos.
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

        <form method="POST" action="{{ route('admin.partidas.store') }}" class="mt-8">
            @csrf

            <fieldset class="grid gap-x-5 gap-y-6 rounded-marca-lg border border-linea bg-blanco p-6 sm:grid-cols-2 sm:p-8">
                <legend class="sr-only">Datos de la partida</legend>

                <div>
                    <label for="fecha" class="etiqueta">Fecha</label>
                    <input type="date" id="fecha" name="fecha" required
                           value="{{ old('fecha', $partida->fecha?->toDateString()) }}"
                           @error('fecha') aria-invalid="true" aria-describedby="fecha-error" @enderror
                           class="campo tabular-nums" />
                    @error('fecha') <p id="fecha-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="asiento" class="etiqueta">Número de asiento</label>
                    <input type="number" id="asiento" name="asiento" min="1" step="1" required
                           value="{{ old('asiento', $partida->asiento) }}"
                           aria-describedby="asiento-ayuda @error('asiento') asiento-error @enderror"
                           @error('asiento') aria-invalid="true" @enderror
                           class="campo tabular-nums" />
                    <p id="asiento-ayuda" class="ayuda">Se sugiere el siguiente número disponible.</p>
                    @error('asiento') <p id="asiento-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="concepto" class="etiqueta">Concepto</label>
                    <input type="text" id="concepto" name="concepto" maxlength="255" required
                           placeholder="Ingreso por ventas del mes"
                           value="{{ old('concepto', $partida->concepto) }}"
                           @error('concepto') aria-invalid="true" aria-describedby="concepto-error" @enderror
                           class="campo" />
                    @error('concepto') <p id="concepto-error" class="error-campo">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-linea pt-6 sm:col-span-2 sm:grid sm:grid-cols-2 sm:gap-x-5">
                    <div>
                        <label for="tipodocumento" class="etiqueta">Tipo de documento</label>
                        <select id="tipodocumento" name="tipodocumento" required
                                @error('tipodocumento') aria-invalid="true" aria-describedby="tipodocumento-error" @enderror
                                class="campo">
                            <option value="" disabled @selected(! $tipoActual)>Selecciona un tipo</option>
                            @foreach (\App\Enums\TipoDocumento::cases() as $tipo)
                                <option value="{{ $tipo->value }}" @selected($tipoActual === $tipo->value)>{{ $tipo->value }}</option>
                            @endforeach
                        </select>
                        @error('tipodocumento') <p id="tipodocumento-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6 sm:mt-0">
                        <label for="numerodocumento" class="etiqueta">Número de documento</label>
                        <input type="text" id="numerodocumento" name="numerodocumento" maxlength="100" required
                               placeholder="P-01"
                               value="{{ old('numerodocumento', $partida->numerodocumento) }}"
                               @error('numerodocumento') aria-invalid="true" aria-describedby="numerodocumento-error" @enderror
                               class="campo" />
                        @error('numerodocumento') <p id="numerodocumento-error" class="error-campo">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <section aria-labelledby="titulo-movimientos" class="mt-10 border-t border-linea pt-10">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="max-w-2xl">
                        <h2 id="titulo-movimientos" class="text-xl font-semibold text-azul">Movimientos</h2>
                        <p class="mt-2 text-[15px] leading-relaxed text-tinta-suave">
                            Cada movimiento lleva una cuenta y un monto en cargo o en abono. Se necesitan al menos dos.
                        </p>
                    </div>
                    <button type="button" id="btn-add-movimiento" class="btn btn-secundario">
                        <x-icono nombre="mas" class="size-5" />
                        Agregar movimiento
                    </button>
                </div>

                <div class="mt-6 overflow-x-auto rounded-marca-lg border border-linea bg-blanco">
                    <table class="w-full min-w-[44rem] border-collapse text-left" id="tabla-movimientos">
                        <thead class="border-b border-linea text-sm text-tinta-suave">
                            <tr>
                                <th scope="col" class="px-3 py-3 pl-5 font-medium">Cuenta</th>
                                <th scope="col" class="w-44 px-3 py-3 font-medium">Cargo</th>
                                <th scope="col" class="w-44 px-3 py-3 font-medium">Abono</th>
                                <th scope="col" class="w-14 px-3 py-3 pr-5"><span class="sr-only">Quitar</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-linea">
                            @foreach ($movimientos as $idx => $fila)
                                @include('admin.contables.partidas._movimiento', ['idx' => $idx, 'fila' => $fila])
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-linea bg-neutro/60 text-[15px]">
                            <tr>
                                <th scope="row" class="px-3 py-3.5 pl-5 text-right font-medium text-tinta-suave">Totales</th>
                                <td class="px-3 py-3.5 pl-6 font-semibold text-azul tabular-nums" id="total-cargo">$0.00</td>
                                <td class="px-3 py-3.5 pl-6 font-semibold text-azul tabular-nums" id="total-abono">$0.00</td>
                                <td class="px-3 py-3.5 pr-5"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p id="estado-cuadre" role="status" aria-live="polite" class="mt-3 text-sm font-medium text-tinta-suave"></p>
            </section>
            
            <div class="sticky bottom-0 z-10 -mx-4 mt-12 border-t border-linea bg-neutro/95 px-4 py-4 backdrop-blur-sm sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.partidas.index') }}" class="btn btn-secundario">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primario px-6">
                        <x-icono nombre="check" class="size-5" />
                        Guardar partida
                    </button>
                </div>
            </div>
        </form>
    </div>

    <template id="plantilla-movimiento">
        @include('admin.contables.partidas._movimiento', ['idx' => '__IDX__', 'fila' => []])
    </template>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tbody = document.querySelector("#tabla-movimientos tbody");
            const plantilla = document.getElementById("plantilla-movimiento");
            const totalCargo = document.getElementById("total-cargo");
            const totalAbono = document.getElementById("total-abono");
            const estado = document.getElementById("estado-cuadre");
            const formato = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" });
            
            let siguienteIdx = tbody.rows.length;

            function centavos(input) {
                const valor = parseFloat(input.value);
                return Number.isFinite(valor) ? Math.round(valor * 100) : 0;
            }

            function recalcular() {
                let cargo = 0;
                let abono = 0;
                tbody.querySelectorAll("[data-monto=cargo]").forEach((i) => (cargo += centavos(i)));
                tbody.querySelectorAll("[data-monto=abono]").forEach((i) => (abono += centavos(i)));

                totalCargo.textContent = formato.format(cargo / 100);
                totalAbono.textContent = formato.format(abono / 100);

                estado.classList.remove("text-exito", "text-peligro", "text-tinta-suave");
                if (cargo === 0 && abono === 0) {
                    estado.textContent = "";
                } else if (cargo === abono) {
                    estado.textContent = "La partida está cuadrada.";
                    estado.classList.add("text-exito");
                } else {
                    estado.textContent = "Diferencia de " + formato.format(Math.abs(cargo - abono) / 100) + " entre cargos y abonos.";
                    estado.classList.add("text-peligro");
                }
            }

            document.getElementById("btn-add-movimiento").addEventListener("click", function () {
                const html = plantilla.innerHTML.replaceAll("__IDX__", siguienteIdx++);
                tbody.insertAdjacentHTML("beforeend", html);
                tbody.lastElementChild.querySelector("select").focus();
            });

            tbody.addEventListener("input", function (e) {
                const tipo = e.target.dataset.monto;
                if (!tipo) return;

                // Un movimiento es cargo o abono: al escribir en uno se limpia el otro.
                if (e.target.value !== "" && parseFloat(e.target.value) > 0) {
                    const otro = e.target.closest("tr").querySelector(`[data-monto=${tipo === "cargo" ? "abono" : "cargo"}]`);
                    otro.value = "";
                }
                recalcular();
            });

            tbody.addEventListener("click", function (e) {
                const boton = e.target.closest(".btn-remove-row");
                if (boton) {
                    boton.closest("tr").remove();
                    recalcular();
                }
            });

            recalcular();
        });
    </script>
@endsection
