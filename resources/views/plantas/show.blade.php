@extends('layouts.app')

@section('content')
<div class="grid cols-2">
    <div class="card">
        <h1 style="margin-top:0;">{{ $planta->codigo }}</h1>
        <p class="muted">{{ $planta->variedad?->nombre }} | {{ $planta->estado }}</p>
        <p><strong>Origen:</strong> {{ $planta->origen }}<br><strong>Lote:</strong> {{ $planta->lote?->nombre ?? '-' }}<br><strong>Etapa:</strong> {{ $planta->etapaFenologica?->nombre ?? '-' }}</p>
        <div class="grid cols-2">
            <div class="card" style="background:#faf7f2;">
                <div class="muted">Altura</div>
                <div class="stat" style="font-size:22px;">{{ $planta->ultima_altura ?? '-' }}</div>
            </div>
            <div class="card" style="background:#faf7f2;">
                <div class="muted">Esbeltez</div>
                <div class="stat" style="font-size:22px;">{{ $planta->indice_esbeltez ?? '-' }}</div>
            </div>
        </div>
        @if (auth()->user()->isAdmin())
            <p style="margin-top:16px;"><a class="btn" href="{{ route('plantas.edit', $planta) }}">Editar planta</a></p>
        @endif
        <div class="actions" style="margin-top:12px;">
            <a class="btn" href="{{ route('captura.create', ['mediciones', 'planta_id' => $planta->id]) }}">Medir</a>
            <a class="btn" href="{{ route('captura.create', ['evaluaciones', 'planta_id' => $planta->id]) }}">Evaluar</a>
            <a class="btn" href="{{ route('captura.create', ['fotos', 'planta_id' => $planta->id]) }}">Foto</a>
            <a class="btn" href="{{ route('captura.create', ['eventos']) }}">Evento</a>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Cambiar estado</h3>
        <form method="POST" action="{{ route('plantas.estado', $planta) }}">
            @csrf
            <div class="field">
                <label>Estado nuevo</label>
                <input name="estado_nuevo" placeholder="muerta / vendida / seleccionada">
            </div>
            <div class="field">
                <label>Motivo</label>
                <input name="motivo">
            </div>
            <div class="field">
                <label>Fecha</label>
                <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}">
            </div>
            <button class="btn primary" type="submit">Guardar estado</button>
        </form>
    </div>
</div>

<div class="grid cols-2" style="margin-top:16px;">
    <div class="card">
        <h3 style="margin-top:0;">Linea de tiempo</h3>
        <div class="timeline">
            @foreach ($timeline as $item)
                <div class="timeline-item">
                    <div class="pill">{{ $item['titulo'] }}</div>
                    <div><strong>{{ $item['fecha'] }}</strong></div>
                    <div class="small">{{ $item['detalle'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h3 style="margin-top:0;">Acceso publico</h3>
        <p class="muted small">{{ route('public.planta', $planta->token_publico) }}</p>
        <p><span class="pill">{{ $planta->publico_activo ? 'Activo' : 'Inactivo' }}</span></p>
    </div>
</div>
@endsection
