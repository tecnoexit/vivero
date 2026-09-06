<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use Illuminate\View\View;

class PublicPlantController extends Controller
{
    public function show(string $token): View
    {
        $planta = Planta::with(['variedad', 'lote', 'mediciones', 'fotos.tipoFoto'])
            ->where('token_publico', $token)
            ->where('publico_activo', true)
            ->firstOrFail();

        return view('public.planta', [
            'planta' => $planta,
            'timeline' => $planta->timeline(),
        ]);
    }
}
