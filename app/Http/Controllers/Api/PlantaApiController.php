<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Planta;
use Illuminate\Http\JsonResponse;

class PlantaApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Planta::with(['variedad', 'lote', 'etapaFenologica'])
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function show(Planta $planta): JsonResponse
    {
        $planta->load(['variedad', 'proveedor', 'bandeja', 'etapaFenologica', 'lote', 'mediciones', 'evaluaciones', 'eventos.tipoEvento', 'fotos.tipoFoto', 'cambiosEstado']);

        return response()->json([
            'data' => $planta,
            'timeline' => $planta->timeline(),
        ]);
    }
}
