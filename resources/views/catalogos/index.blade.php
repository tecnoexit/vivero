@extends('layouts.app')

@section('content')
<div class="actions" style="justify-content:space-between; margin-bottom:16px;">
    <div>
        <h1 style="margin:0;">{{ $config['title'] }}</h1>
        <div class="muted">Administracion basica del catalogo.</div>
    </div>
    <a class="btn primary" href="{{ route('catalogos.create', $resource) }}">Nuevo</a>
</div>

<div class="card">
    <table>
        <thead>
        <tr>
            @foreach ($config['columns'] as $column)
                <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
            @endforeach
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $item)
            <tr>
                @foreach ($config['columns'] as $column)
                    <td>
                        @php($value = data_get($item, $column))
                        @if (str_ends_with($column, '_id'))
                            @php($relation = str($column)->beforeLast('_id')->camel()->toString())
                            {{ data_get($item, $relation.'.nombre') ?? data_get($item, $relation.'.codigo') ?? $value }}
                        @elseif (is_bool($value))
                            {{ $value ? 'Si' : 'No' }}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                @endforeach
                <td class="actions">
                    <a class="btn" href="{{ route('catalogos.edit', [$resource, $item->id]) }}">Editar</a>
                    <form method="POST" action="{{ route('catalogos.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('Eliminar registro?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit">Borrar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $items->links() }}</div>
</div>
@endsection
