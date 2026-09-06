@extends('layouts.app')

@section('content')
<div class="card">
    <h1 style="margin-top:0;">Plantas</h1>
    <table>
        <thead>
        <tr>
            <th>Codigo</th><th>Variedad</th><th>Lote</th><th>Estado</th><th>Vigor</th><th>Sanidad</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($plantas as $planta)
            <tr>
                <td><a href="{{ route('plantas.show', $planta) }}">{{ $planta->codigo }}</a></td>
                <td>{{ $planta->variedad?->nombre }}</td>
                <td>{{ $planta->lote?->nombre }}</td>
                <td>{{ $planta->estado }}</td>
                <td>{{ $planta->score_vigor_actual ?? '-' }}</td>
                <td>{{ $planta->score_sanidad_actual ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px;">{{ $plantas->links() }}</div>
</div>
@endsection
