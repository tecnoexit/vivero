<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogController extends Controller
{
    protected function resourceConfig(string $resource): array
    {
        $config = config("nursery.resources.$resource");

        abort_unless($config, 404);

        return $config;
    }

    public function index(string $resource): View
    {
        $config = $this->resourceConfig($resource);
        $model = $config['model'];

        return view('catalogos.index', [
            'resource' => $resource,
            'config' => $config,
            'items' => $model::with($this->relationsFor($config))->orderByDesc('id')->paginate(15),
        ]);
    }

    public function create(string $resource): View
    {
        $config = $this->resourceConfig($resource);

        return view('catalogos.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => new ($config['model'])(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        $data = $this->validatedData($request, $config);
        $item = new ($config['model'])();
        if (in_array('autor_id', $item->getFillable(), true) && ! isset($data['autor_id'])) {
            $data['autor_id'] = $request->user()?->id;
        }
        $this->fillModel($item, $data, $request, $config);
        $item->save();
        $this->syncRelations($item, $data, $config);

        return redirect()->route('catalogos.index', $resource)->with('status', 'Registro guardado.');
    }

    public function edit(string $resource, int $id): View
    {
        $config = $this->resourceConfig($resource);

        return view('catalogos.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => ($config['model'])::findOrFail($id),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        $item = ($config['model'])::findOrFail($id);
        $data = $this->validatedData($request, $config, $item->id);
        if (in_array('autor_id', $item->getFillable(), true) && ! isset($data['autor_id'])) {
            $data['autor_id'] = $request->user()?->id;
        }
        $this->fillModel($item, $data, $request, $config);
        $item->save();
        $this->syncRelations($item, $data, $config);

        return redirect()->route('catalogos.index', $resource)->with('status', 'Registro actualizado.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $config = $this->resourceConfig($resource);
        $item = ($config['model'])::findOrFail($id);
        $item->delete();

        return redirect()->route('catalogos.index', $resource)->with('status', 'Registro eliminado.');
    }

    protected function relationsFor(array $config): array
    {
        $relations = [];

        foreach ($config['fields'] as $field) {
            if (! empty($field['relation'])) {
                $relations[] = $field['type'] === 'multiselect'
                    ? Str::camel(Str::beforeLast($field['name'], '_ids'))
                    : Str::camel(Str::beforeLast($field['name'], '_id'));
            }
        }

        return array_values(array_unique($relations));
    }

    protected function validatedData(Request $request, array $config, ?int $ignoreId = null): array
    {
        $rules = [];

        foreach ($config['fields'] as $field) {
            $rules[$field['name']] = $field['rules'] ?? 'nullable';

            if ($ignoreId && $field['name'] === 'codigo') {
                $rules[$field['name']] = [Rule::unique('plantas', 'codigo')->ignore($ignoreId)];
            }
        }

        return $request->validate($rules);
    }

    protected function fillModel(Model $item, array $data, Request $request, array $config): void
    {
        foreach ($config['fields'] as $field) {
            if (($field['type'] ?? null) === 'file' && $request->hasFile($field['name'])) {
                $data[$field['name']] = $request->file($field['name'])->store('uploads', 'public');
            }

            if (($field['type'] ?? null) === 'checkbox') {
                $data[$field['name']] = $request->boolean($field['name']);
            }
        }

        $item->fill(Arr::only($data, $item->getFillable()));
    }

    protected function syncRelations(Model $item, array $data, array $config): void
    {
        if ($item instanceof \App\Models\Evento && array_key_exists('plantas_ids', $data)) {
            $item->plantas()->sync($data['plantas_ids'] ?? []);
            $item->plantas()->get()->each(fn (\App\Models\Planta $planta) => $planta->recalcularMetricas());
        }

        if ($item instanceof \App\Models\Foto && $item->activa) {
            $item->planta?->recalcularMetricas();
        }
    }
}
