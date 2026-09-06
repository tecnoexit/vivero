@extends('layouts.app')

@section('content')
<div class="card">
    <h1 style="margin-top:0;">Generar etiquetas</h1>
    <form method="POST" action="{{ route('etiquetas.store') }}">
        @csrf
        <div class="grid cols-3">
            <div class="field">
                <label>Variedad</label>
                <select name="variedad_id" required>
                    @foreach ($variedades as $variedad)
                        <option value="{{ $variedad->id }}">{{ $variedad->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Cantidad</label>
                <input type="number" name="cantidad" min="1" max="500" value="10" required>
            </div>
        </div>

        <div class="field">
            <label>Formatos</label>
            <div class="actions">
                <label><input type="checkbox" name="formats[]" value="numero" checked> Numérico</label>
                <label><input type="checkbox" name="formats[]" value="qr"> QR</label>
                <label><input type="checkbox" name="formats[]" value="code128"> Code128</label>
            </div>
        </div>

        <button class="btn primary" type="submit">Generar vista imprimible</button>
    </form>
</div>
@endsection
