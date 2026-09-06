<?php

namespace App\Http\Controllers;

use App\Models\Bandeja;
use App\Models\CambioEstado;
use App\Models\Evaluacion;
use App\Models\Evento;
use App\Models\Foto;
use App\Models\Lote;
use App\Models\Medicion;
use App\Models\Planta;
use App\Models\Proveedor;
use App\Models\Variedad;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'conteos' => [
                'variedades' => Variedad::count(),
                'proveedores' => Proveedor::count(),
                'bandejas' => Bandeja::count(),
                'lotes' => Lote::count(),
                'plantas' => Planta::count(),
                'mediciones' => Medicion::count(),
                'evaluaciones' => Evaluacion::count(),
                'eventos' => Evento::count(),
                'fotos' => Foto::count(),
                'cambios' => CambioEstado::count(),
            ],
            'ultimas_plantas' => Planta::with(['variedad', 'lote'])
                ->orderByDesc('id')
                ->limit(8)
                ->get(),
            'plantas_seleccion' => Planta::with('variedad')
                ->orderByDesc('score_vigor_actual')
                ->limit(5)
                ->get(),
        ]);
    }
}
