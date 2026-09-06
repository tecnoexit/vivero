<?php

namespace App\Http\Controllers;

use App\Models\CambioEstado;
use App\Models\Planta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlantaController extends Controller
{
    public function index(): View
    {
        return view('plantas.index', [
            'plantas' => Planta::with(['variedad', 'lote', 'etapaFenologica'])
                ->orderByDesc('id')
                ->paginate(20),
        ]);
    }

    public function show(Planta $planta): View
    {
        $planta->load(['variedad', 'proveedor', 'bandeja', 'etapaFenologica', 'lote', 'mediciones', 'evaluaciones', 'eventos.tipoEvento', 'fotos.tipoFoto', 'cambiosEstado']);

        return view('plantas.show', [
            'planta' => $planta,
            'timeline' => $planta->timeline(),
        ]);
    }

    public function edit(Planta $planta): View
    {
        return view('catalogos.form', [
            'resource' => 'plantas',
            'config' => config('nursery.resources.plantas'),
            'item' => $planta,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Planta $planta): RedirectResponse
    {
        $config = config('nursery.resources.plantas');
        $rules = [];

        foreach ($config['fields'] as $field) {
            $rules[$field['name']] = $field['name'] === 'codigo'
                ? ['nullable', 'string', 'max:255', 'unique:plantas,codigo,'.$planta->id]
                : ($field['rules'] ?? 'nullable');
        }

        $data = $request->validate($rules);
        $planta->fill($data);
        $planta->save();

        return redirect()->route('plantas.show', $planta)->with('status', 'Planta actualizada.');
    }

    public function changeState(Request $request, Planta $planta): RedirectResponse
    {
        $data = $request->validate([
            'estado_nuevo' => ['required', 'string', 'max:255'],
            'motivo' => ['nullable', 'string', 'max:255'],
            'fecha' => ['required', 'date'],
        ]);

        $estadoAnterior = $planta->estado;
        $planta->estado = $data['estado_nuevo'];
        $planta->fecha_baja = in_array($data['estado_nuevo'], ['muerta', 'vendida', 'regalada', 'descartada'], true) ? $data['fecha'] : $planta->fecha_baja;
        $planta->motivo_baja = $data['motivo'] ?? $planta->motivo_baja;
        $planta->save();

        CambioEstado::create([
            'planta_id' => $planta->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $data['estado_nuevo'],
            'motivo' => $data['motivo'] ?? null,
            'fecha' => $data['fecha'],
            'autor_id' => $request->user()->id,
        ]);

        return redirect()->route('plantas.show', $planta)->with('status', 'Estado actualizado.');
    }
}
