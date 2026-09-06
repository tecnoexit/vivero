@extends('layouts.app')

@section('content')
<style>
    @media print {
        .topbar, .print-hide { display:none !important; }
        body { background:#fff; }
        .card { box-shadow:none; border-color:#999; }
    }
    .label-grid { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:12px; }
    .label { border:1px dashed #999; border-radius:12px; padding:16px; min-height:120px; display:flex; flex-direction:column; justify-content:space-between; }
    .label-code { font-size:24px; font-weight:700; letter-spacing:.08em; }
    .format-list { display:flex; gap:8px; flex-wrap:wrap; }
</style>

<div class="card print-hide" style="margin-bottom:16px;">
    <h1 style="margin-top:0;">Vista de etiquetas</h1>
    <p class="muted">Variedad: <strong>{{ $variedad->nombre }}</strong> | Cantidad: <strong>{{ $cantidad }}</strong></p>
    <button class="btn primary" onclick="window.print()">Imprimir</button>
    <a class="btn" href="{{ route('etiquetas.create') }}">Volver</a>
</div>

<div class="label-grid">
    @foreach ($codes as $code)
        @foreach ($formats as $format)
            <div class="label card">
                <div class="format-list">
                    <span class="pill">{{ strtoupper($format) }}</span>
                    <span class="pill">{{ $variedad->nombre }}</span>
                </div>
                <div>
                    <div class="label-code">{{ $code }}</div>
                    <div class="small muted">Etiqueta preemitida para asignacion posterior</div>
                </div>
                <div class="small">{{ $format === 'numero' ? 'Codigo numerico' : ($format === 'qr' ? 'QR textual en esta vista' : 'Code128 textual en esta vista') }}</div>
            </div>
        @endforeach
    @endforeach
</div>
@endsection
