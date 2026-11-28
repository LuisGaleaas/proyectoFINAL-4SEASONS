@php($fila = $fila ?? [])
<tr>
    <td class="px-3 py-2.5 pl-5">
        <select name="detalles[{{ $idx }}][cuenta_id]" aria-label="Cuenta"
                @error("detalles.$idx.cuenta_id") aria-invalid="true" @enderror
                class="campo campo-compacto">
            <option value="">Seleccionar cuenta</option>
            @foreach ($cuentas as $c)
                <option value="{{ $c->id }}" @selected((string) ($fila['cuenta_id'] ?? '') === (string) $c->id)>
                    {{ $c->codigo }} · {{ $c->cuenta }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="px-3 py-2.5">
        <div class="campo-unidad">
            <span>$</span>
            <input type="number" name="detalles[{{ $idx }}][cargo]" step="0.01" min="0"
                   value="{{ $fila['cargo'] ?? '' }}"
                   aria-label="Cargo" data-monto="cargo"
                   @error("detalles.$idx.cargo") aria-invalid="true" @enderror
                   class="campo campo-compacto tabular-nums" />
        </div>
    </td>
    <td class="px-3 py-2.5">
        <div class="campo-unidad">
            <span>$</span>
            <input type="number" name="detalles[{{ $idx }}][abono]" step="0.01" min="0"
                   value="{{ $fila['abono'] ?? '' }}"
                   aria-label="Abono" data-monto="abono"
                   @error("detalles.$idx.abono") aria-invalid="true" @enderror
                   class="campo campo-compacto tabular-nums" />
        </div>
    </td>
    <td class="px-3 py-2.5 pr-5 text-right">
        <button type="button" class="btn-remove-row btn-icono" aria-label="Quitar movimiento">
            <x-icono nombre="cerrar" class="size-4" />
        </button>
    </td>
</tr>
