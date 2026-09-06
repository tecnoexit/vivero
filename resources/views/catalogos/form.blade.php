@extends('layouts.app')

@section('content')
<div class="card">
    <h1 style="margin-top:0;">{{ $mode === 'create' ? 'Nuevo' : 'Editar' }} {{ $config['title'] }}</h1>

    <form method="POST" action="{{ $mode === 'create' ? route('catalogos.store', $resource) : route('catalogos.update', [$resource, $item->id]) }}" enctype="multipart/form-data">
        @csrf
        @if ($mode === 'edit')
            @method('PUT')
        @endif

        @foreach ($config['fields'] as $field)
            @php
                $value = old($field['name'], data_get($item, $field['name']));
                $type = $field['type'] ?? 'text';
            @endphp
            <div class="field">
                <label>{{ $field['label'] }}</label>
                @if ($type === 'textarea')
                    <textarea name="{{ $field['name'] }}">{{ $value }}</textarea>
                @elseif ($type === 'select')
                    <select name="{{ $field['name'] }}">
                        <option value="">Seleccione</option>
                        @foreach (($field['options'] ?? ($field['relation'] ? ($field['relation'])::orderBy('id')->get() : [])) as $optionValue => $optionLabel)
                            @if ($field['options'] ?? false)
                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                            @else
                                @php
                                    $option = $optionLabel;
                                    $label = $option instanceof \App\Models\Planta
                                        ? $option->codigo.' - '.($option->variedad?->nombre ?? '')
                                        : ($option instanceof \App\Models\Bandeja
                                            ? 'Bandeja #'.$option->id.' - '.($option->variedad?->nombre ?? '')
                                            : ($option->codigo ?? $option->nombre ?? ('#'.$option->id)));
                                @endphp
                                <option value="{{ $option->id }}" @selected((string) $value === (string) $option->id)>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                @elseif ($type === 'multiselect')
                    @php($selected = old($field['name'], $item->plantas?->pluck('id')->all() ?? []))
                    <select name="{{ $field['name'] }}[]" multiple>
                        @foreach (($field['relation'])::orderBy('codigo')->get() as $option)
                            <option value="{{ $option->id }}" @selected(in_array($option->id, $selected, true))>{{ $option->codigo }} - {{ $option->variedad?->nombre }}</option>
                        @endforeach
                    </select>
                @elseif ($type === 'checkbox')
                    <label style="display:flex; gap:8px; align-items:center; margin:0; font-weight:normal;">
                        <input type="checkbox" name="{{ $field['name'] }}" value="1" @checked((bool) $value) style="width:auto;">
                        Activar
                    </label>
                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $field['name'] }}"
                        value="{{ $type === 'file' ? '' : $value }}"
                        @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                    >
                    @if ($type === 'file' && !empty(data_get($item, $field['name'])))
                        <div class="small muted" style="margin-top:8px;">Archivo actual: {{ data_get($item, $field['name']) }}</div>
                    @endif
                @endif
                @error($field['name'])<div class="error">{{ $message }}</div>@enderror
            </div>
        @endforeach

        <button class="btn primary" type="submit">Guardar</button>
        <a class="btn" href="{{ route('catalogos.index', $resource) }}">Volver</a>
    </form>
</div>
@endsection
