<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabelController extends Controller
{
    public function create(): View
    {
        return view('etiquetas.create', [
            'variedades' => Variedad::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): View
    {
        $data = $request->validate([
            'variedad_id' => ['required', 'integer', 'exists:variedades,id'],
            'cantidad' => ['required', 'integer', 'min:1', 'max:500'],
            'formats' => ['nullable', 'array'],
            'formats.*' => ['in:numero,qr,code128'],
        ]);

        $variedad = Variedad::findOrFail($data['variedad_id']);
        $formats = array_values(array_unique($data['formats'] ?? ['numero']));
        $codes = $this->generateCodes($variedad, (int) $data['cantidad']);

        return view('etiquetas.preview', [
            'variedad' => $variedad,
            'cantidad' => (int) $data['cantidad'],
            'formats' => $formats,
            'codes' => $codes,
        ]);
    }

    private function generateCodes(Variedad $variedad, int $cantidad): array
    {
        $prefix = $variedad->prefijoCodigo();
        $existing = Planta::query()
            ->where('codigo', 'like', $prefix.'-%')
            ->pluck('codigo')
            ->all();

        $max = 0;
        foreach ($existing as $code) {
            if (preg_match('/^'.preg_quote($prefix, '/').'-(\d{4})$/', $code, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        $codes = [];
        for ($i = $max + 1; count($codes) < $cantidad; $i++) {
            $codes[] = sprintf('%s-%04d', $prefix, $i);
        }

        return $codes;
    }
}
