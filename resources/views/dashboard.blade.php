@extends('layouts.app')

@section('content')
<div class="grid cols-3">
    @foreach ($conteos as $label => $value)
        <div class="card">
            <div class="muted">{{ ucfirst($label) }}</div>
            <div class="stat">{{ $value }}</div>
        </div>
    @endforeach
</div>

<div class="grid cols-2" style="margin-top:16px;">
    <div class="card">
        <h3 style="margin-top:0;">Plantas recientes</h3>
        <table>
            <thead><tr><th>Codigo</th><th>Variedad</th><th>Estado</th></tr></thead>
            <tbody>
                @foreach ($ultimas_plantas as $planta)
                    <tr>
                        <td><a href="{{ route('plantas.show', $planta) }}">{{ $planta->codigo }}</a></td>
                        <td>{{ $planta->variedad?->nombre }}</td>
                        <td>{{ $planta->estado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3 style="margin-top:0;">Seleccion preliminar</h3>
        <table>
            <thead><tr><th>Codigo</th><th>Vigor</th><th>Sanidad</th></tr></thead>
            <tbody>
                @foreach ($plantas_seleccion as $planta)
                    <tr>
                        <td>{{ $planta->codigo }}</td>
                        <td>{{ $planta->score_vigor_actual ?? '-' }}</td>
                        <td>{{ $planta->score_sanidad_actual ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
