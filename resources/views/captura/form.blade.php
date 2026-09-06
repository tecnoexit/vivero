@extends('layouts.app')

@section('content')
<div class="card">
    <h1 style="margin-top:0;">{{ $title }}</h1>
    <form method="POST" action="{{ route('captura.store', $type) }}" enctype="multipart/form-data">
        @csrf

        @foreach ($fields as $field)
            @php
                $value = old($field, $field === 'planta_id' ? $plantaId : null);
            @endphp
            <div class="field">
                <label>{{ ucfirst(str_replace('_', ' ', $field)) }}</label>

                @if ($field === 'planta_id')
                    <select name="planta_id" required>
                        <option value="">Seleccione</option>
                        @foreach ($plantas as $planta)
                            <option value="{{ $planta->id }}" @selected((string) $value === (string) $planta->id)>
                                {{ $planta->codigo }} - {{ $planta->variedad?->nombre }}
                            </option>
                        @endforeach
                    </select>
                @elseif ($field === 'tipo_evento_id')
                    <select name="tipo_evento_id" required>
                        <option value="">Seleccione</option>
                        @foreach ($tiposEvento as $tipo)
                            <option value="{{ $tipo->id }}" @selected((string) $value === (string) $tipo->id)>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                @elseif ($field === 'tipo_foto_id')
                    <select name="tipo_foto_id" required>
                        <option value="">Seleccione</option>
                        @foreach ($tiposFoto as $tipo)
                            <option value="{{ $tipo->id }}" @selected((string) $value === (string) $tipo->id)>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                @elseif ($field === 'evento_id')
                    <select name="evento_id">
                        <option value="">Opcional</option>
                        @foreach (\App\Models\Evento::orderByDesc('id')->limit(100)->get() as $evento)
                            <option value="{{ $evento->id }}" @selected((string) $value === (string) $evento->id)>#{{ $evento->id }} {{ $evento->tipoEvento?->nombre }}</option>
                        @endforeach
                    </select>
                @elseif ($field === 'plantas_ids')
                    <select name="plantas_ids[]" multiple required>
                        @foreach ($plantas as $planta)
                            <option value="{{ $planta->id }}">{{ $planta->codigo }} - {{ $planta->variedad?->nombre }}</option>
                        @endforeach
                    </select>
                @elseif ($field === 'imagen')
                    <input type="file" name="imagen" accept="image/*" required>
                @elseif ($field === 'activa')
                    <label style="display:flex; gap:8px; align-items:center; font-weight:normal; margin:0;">
                        <input type="checkbox" name="activa" value="1" style="width:auto;">
                        Marcar como activa
                    </label>
                @elseif ($field === 'notas')
                    <textarea name="notas">{{ old('notas') }}</textarea>
                @elseif ($field === 'fecha')
                    <input type="date" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                @elseif (in_array($field, ['altura_cm', 'diametro_tallo_mm', 'longitud_hoja_cm', 'diametro_copa_cm'], true))
                    <input type="number" step="0.01" name="{{ $field }}" value="{{ old($field) }}">
                @elseif ($field === 'n_ramas' || str_starts_with($field, 'score_'))
                    <input type="number" name="{{ $field }}" value="{{ old($field) }}">
                @else
                    <input type="text" name="{{ $field }}" value="{{ old($field) }}">
                @endif

                @error($field)<div class="error">{{ $message }}</div>@enderror
            </div>
        @endforeach

        <div class="actions">
            <button class="btn primary" type="submit">Guardar</button>
            @if ($plantaId)
                <a class="btn" href="{{ route('plantas.show', $plantaId) }}">Volver a planta</a>
            @endif
        </div>
    </form>
</div>
@endsection
