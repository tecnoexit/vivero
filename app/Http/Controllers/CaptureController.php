<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Evaluacion;
use App\Models\Foto;
use App\Models\Medicion;
use App\Models\Planta;
use App\Models\TipoEvento;
use App\Models\TipoFoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CaptureController extends Controller
{
    public function create(string $type, Request $request): View
    {
        $config = $this->config($type);

        return view('captura.form', [
            'type' => $type,
            'title' => $config['title'],
            'fields' => $config['fields'],
            'plantas' => Planta::with('variedad')->orderBy('codigo')->get(),
            'tiposEvento' => TipoEvento::orderBy('nombre')->get(),
            'tiposFoto' => TipoFoto::orderBy('nombre')->get(),
            'plantaId' => $request->integer('planta_id'),
        ]);
    }

    public function store(string $type, Request $request): RedirectResponse
    {
        return match ($type) {
            'mediciones' => $this->storeMedicion($request),
            'evaluaciones' => $this->storeEvaluacion($request),
            'eventos' => $this->storeEvento($request),
            'fotos' => $this->storeFoto($request),
            default => abort(404),
        };
    }

    private function config(string $type): array
    {
        return match ($type) {
            'mediciones' => [
                'title' => 'Nueva medicion',
                'fields' => ['planta_id', 'fecha', 'altura_cm', 'diametro_tallo_mm', 'longitud_hoja_cm', 'diametro_copa_cm', 'n_ramas', 'notas'],
            ],
            'evaluaciones' => [
                'title' => 'Nueva evaluacion',
                'fields' => ['planta_id', 'fecha', 'score_vigor', 'score_sanidad', 'notas'],
            ],
            'eventos' => [
                'title' => 'Nuevo evento',
                'fields' => ['tipo_evento_id', 'fecha', 'producto', 'dosis', 'plantas_ids', 'notas'],
            ],
            'fotos' => [
                'title' => 'Nueva foto',
                'fields' => ['planta_id', 'evento_id', 'tipo_foto_id', 'fecha', 'imagen', 'activa'],
            ],
            default => abort(404),
        };
    }

    private function storeMedicion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'planta_id' => ['required', 'integer', 'exists:plantas,id'],
            'fecha' => ['required', 'date'],
            'altura_cm' => ['nullable', 'numeric'],
            'diametro_tallo_mm' => ['nullable', 'numeric'],
            'longitud_hoja_cm' => ['nullable', 'numeric'],
            'diametro_copa_cm' => ['nullable', 'numeric'],
            'n_ramas' => ['nullable', 'integer', 'min:0'],
            'notas' => ['nullable', 'string'],
        ]);

        $data['autor_id'] = $request->user()->id;
        Medicion::create($data);

        return redirect()->route('plantas.show', $data['planta_id'])->with('status', 'Medicion guardada.');
    }

    private function storeEvaluacion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'planta_id' => ['required', 'integer', 'exists:plantas,id'],
            'fecha' => ['required', 'date'],
            'score_vigor' => ['required', 'integer', 'min:1', 'max:5'],
            'score_sanidad' => ['required', 'integer', 'min:1', 'max:5'],
            'notas' => ['nullable', 'string'],
        ]);

        $data['autor_id'] = $request->user()->id;
        Evaluacion::create($data);

        return redirect()->route('plantas.show', $data['planta_id'])->with('status', 'Evaluacion guardada.');
    }

    private function storeEvento(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo_evento_id' => ['required', 'integer', 'exists:tipo_eventos,id'],
            'fecha' => ['required', 'date'],
            'producto' => ['nullable', 'string', 'max:255'],
            'dosis' => ['nullable', 'string', 'max:255'],
            'plantas_ids' => ['required', 'array', 'min:1'],
            'plantas_ids.*' => ['integer', 'exists:plantas,id'],
            'notas' => ['nullable', 'string'],
        ]);

        $evento = Evento::create([
            'tipo_evento_id' => $data['tipo_evento_id'],
            'fecha' => $data['fecha'],
            'producto' => $data['producto'] ?? null,
            'dosis' => $data['dosis'] ?? null,
            'notas' => $data['notas'] ?? null,
            'autor_id' => $request->user()->id,
        ]);

        $evento->plantas()->sync($data['plantas_ids']);
        $evento->plantas()->get()->each(fn (Planta $planta) => $planta->recalcularMetricas());

        return redirect()->route('plantas.show', $data['plantas_ids'][0])->with('status', 'Evento guardado.');
    }

    private function storeFoto(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'planta_id' => ['required', 'integer', 'exists:plantas,id'],
            'evento_id' => ['nullable', 'integer', 'exists:eventos,id'],
            'tipo_foto_id' => ['required', 'integer', 'exists:tipo_fotos,id'],
            'fecha' => ['required', 'date'],
            'imagen' => ['required', 'image', 'max:4096'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('imagen')->store('fotos', 'public');

        Foto::create([
            'planta_id' => $data['planta_id'],
            'evento_id' => $data['evento_id'] ?? null,
            'tipo_foto_id' => $data['tipo_foto_id'],
            'imagen' => $path,
            'fecha' => $data['fecha'],
            'activa' => $request->boolean('activa'),
            'autor_id' => $request->user()->id,
        ]);

        return redirect()->route('plantas.show', $data['planta_id'])->with('status', 'Foto guardada.');
    }
}
