@extends('layouts.app')

@section('content')
<div class="card">
    <h1 style="margin-top:0;">{{ $planta->codigo }}</h1>
    <p class="muted">Ficha publica de {{ $planta->variedad?->nombre }}</p>
    <div class="grid cols-3">
        <div><strong>Origen</strong><br>{{ $planta->origen }}</div>
        <div><strong>Etapa</strong><br>{{ $planta->etapaFenologica?->nombre ?? '-' }}</div>
        <div><strong>Lote</strong><br>{{ $planta->lote?->nombre ?? '-' }}</div>
    </div>
</div>

<div class="grid cols-2" style="margin-top:16px;">
    <div class="card">
        <h3 style="margin-top:0;">Mediciones</h3>
        <table>
            <thead><tr><th>Fecha</th><th>Altura</th><th>Diametro</th></tr></thead>
            <tbody>
                @foreach ($planta->mediciones as $medicion)
                    <tr>
                        <td>{{ $medicion->fecha }}</td>
                        <td>{{ $medicion->altura_cm }}</td>
                        <td>{{ $medicion->diametro_tallo_mm }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3 style="margin-top:0;">Fotos</h3>
        @foreach ($planta->fotos as $foto)
            <div class="timeline-item" style="margin-bottom:10px;">
                <div><strong>{{ $foto->tipoFoto?->nombre }}</strong></div>
                <div class="small muted">{{ $foto->fecha }}</div>
                <div class="small">{{ $foto->imagen }}</div>
            </div>
        @endforeach
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <h3 style="margin-top:0;">Cronologia</h3>
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
@endsection
