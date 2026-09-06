@extends('layouts.app')

@section('content')
<div class="grid cols-2">
    <div class="card">
        <h1 style="margin-top:0;">Panel de seleccion</h1>
        <form method="GET" action="{{ route('seleccion.index') }}">
            <div class="grid cols-2">
                <div class="field">
                    <label>Variedad</label>
                    <select name="variedad_id">
                        <option value="">Todas</option>
                        @foreach ($variedades as $variedad)
                            <option value="{{ $variedad->id }}" @selected(($filtros['variedad_id'] ?? null) == $variedad->id)>{{ $variedad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Lote</label>
                    <select name="lote_id">
                        <option value="">Todos</option>
                        @foreach ($lotes as $lote)
                            <option value="{{ $lote->id }}" @selected(($filtros['lote_id'] ?? null) == $lote->id)>{{ $lote->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid cols-3">
                <div class="field"><label>Tasa minima</label><input type="number" step="0.01" name="tasa_min" value="{{ $filtros['tasa_min'] ?? '' }}"></div>
                <div class="field"><label>Esbeltez min</label><input type="number" step="0.01" name="esbeltez_min" value="{{ $filtros['esbeltez_min'] ?? '' }}"></div>
                <div class="field"><label>Esbeltez max</label><input type="number" step="0.01" name="esbeltez_max" value="{{ $filtros['esbeltez_max'] ?? '' }}"></div>
            </div>

            <div class="grid cols-3">
                <div class="field"><label>Vigor minimo</label><input type="number" name="score_vigor_min" value="{{ $filtros['score_vigor_min'] ?? '' }}"></div>
                <div class="field"><label>Sanidad minima</label><input type="number" name="score_sanidad_min" value="{{ $filtros['score_sanidad_min'] ?? '' }}"></div>
                <div class="field"><label>Max fitosanitarios</label><input type="number" name="max_fitosanitarios" value="{{ $filtros['max_fitosanitarios'] ?? '' }}"></div>
            </div>

            <div class="grid cols-2">
                <div class="field">
                    <label>Ordenar por</label>
                    <select name="sort">
                        @foreach ([
                            'score_desempeno' => 'Indice de desempeno',
                            'codigo' => 'Codigo',
                            'tasa_crecimiento' => 'Tasa de crecimiento',
                            'indice_esbeltez' => 'Indice de esbeltez',
                            'score_vigor_actual' => 'Vigor',
                            'score_sanidad_actual' => 'Sanidad',
                            'n_eventos_fitosanitarios' => 'Eventos fitosanitarios',
                        ] as $key => $label)
                            <option value="{{ $key }}" @selected(($filtros['sort'] ?? 'score_desempeno') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Direccion</label>
                    <select name="direction">
                        <option value="desc" @selected(($filtros['direction'] ?? 'desc') === 'desc')>Descendente</option>
                        <option value="asc" @selected(($filtros['direction'] ?? '') === 'asc')>Ascendente</option>
                    </select>
                </div>
            </div>

            <div class="grid cols-2">
                <div class="field"><label>Peso crecimiento</label><input type="number" name="peso_crecimiento" value="{{ $weights['crecimiento'] }}"></div>
                <div class="field"><label>Peso esbeltez</label><input type="number" name="peso_esbeltez" value="{{ $weights['esbeltez'] }}"></div>
                <div class="field"><label>Peso vigor</label><input type="number" name="peso_vigor" value="{{ $weights['vigor'] }}"></div>
                <div class="field"><label>Peso sanidad</label><input type="number" name="peso_sanidad" value="{{ $weights['sanidad'] }}"></div>
            </div>

            <div class="field"><label>Crecimiento de referencia</label><input type="number" step="0.01" name="crecimiento_ref" value="{{ $selection['crecimiento_ref'] }}"></div>

            <div class="actions">
                <button class="btn primary" type="submit">Aplicar</button>
                <a class="btn" href="{{ route('seleccion.export', request()->query()) }}">Exportar CSV</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Criterios activos</h3>
        <p class="muted small">El indice usa crecimiento, esbeltez, vigor y sanidad con los pesos configurados.</p>
        <div class="grid cols-2">
            <div><strong>Registros</strong><br>{{ $plantas->count() }}</div>
            <div><strong>Peso total</strong><br>{{ array_sum($weights) }}</div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Variedad</th>
                <th>Tasa</th>
                <th>Esbeltez</th>
                <th>Vigor</th>
                <th>Sanidad</th>
                <th>Indice</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plantas as $row)
                <tr>
                    <td><a href="{{ route('plantas.show', $row['planta']) }}">{{ $row['planta']->codigo }}</a></td>
                    <td>{{ $row['planta']->variedad?->nombre }}</td>
                    <td>{{ $row['planta']->tasa_crecimiento ?? '-' }}</td>
                    <td>{{ $row['planta']->indice_esbeltez ?? '-' }}</td>
                    <td>{{ $row['planta']->score_vigor_actual ?? '-' }}</td>
                    <td>{{ $row['planta']->score_sanidad_actual ?? '-' }}</td>
                    <td><strong>{{ $row['indice_desempeno'] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
