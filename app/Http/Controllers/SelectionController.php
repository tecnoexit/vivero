<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SelectionController extends Controller
{
    public function index(Request $request): View
    {
        $selection = $this->selectionData($request);

        return view('seleccion.index', $selection);
    }

    public function export(Request $request): StreamedResponse
    {
        $selection = $this->selectionData($request);
        $rows = $selection['plantas'];

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, [
                'codigo',
                'variedad',
                'lote',
                'estado',
                'tasa_crecimiento',
                'indice_esbeltez',
                'vigor',
                'sanidad',
                'eventos_fitosanitarios',
                'indice_desempeno',
            ]);

            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['planta']->codigo,
                    $row['planta']->variedad?->nombre,
                    $row['planta']->lote?->nombre,
                    $row['planta']->estado,
                    $row['planta']->tasa_crecimiento,
                    $row['planta']->indice_esbeltez,
                    $row['planta']->score_vigor_actual,
                    $row['planta']->score_sanidad_actual,
                    $row['planta']->n_eventos_fitosanitarios,
                    $row['indice_desempeno'],
                ]);
            }

            fclose($output);
        }, 'seleccion.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function selectionData(Request $request): array
    {
        $filters = $request->validate([
            'variedad_id' => ['nullable', 'integer', 'exists:variedades,id'],
            'lote_id' => ['nullable', 'integer', 'exists:lotes,id'],
            'estado' => ['nullable', 'string', 'max:255'],
            'origen' => ['nullable', 'string', 'max:255'],
            'score_vigor_min' => ['nullable', 'integer', 'min:1', 'max:5'],
            'score_sanidad_min' => ['nullable', 'integer', 'min:1', 'max:5'],
            'tasa_min' => ['nullable', 'numeric'],
            'esbeltez_min' => ['nullable', 'numeric'],
            'esbeltez_max' => ['nullable', 'numeric'],
            'max_fitosanitarios' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'string', 'in:score_desempeno,codigo,tasa_crecimiento,indice_esbeltez,score_vigor_actual,score_sanidad_actual,n_eventos_fitosanitarios'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
            'peso_crecimiento' => ['nullable', 'integer', 'min:0', 'max:100'],
            'peso_esbeltez' => ['nullable', 'integer', 'min:0', 'max:100'],
            'peso_vigor' => ['nullable', 'integer', 'min:0', 'max:100'],
            'peso_sanidad' => ['nullable', 'integer', 'min:0', 'max:100'],
            'crecimiento_ref' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $query = Planta::with(['variedad', 'lote'])
            ->when($request->filled('variedad_id'), fn ($q) => $q->where('variedad_id', $request->integer('variedad_id')))
            ->when($request->filled('lote_id'), fn ($q) => $q->where('lote_id', $request->integer('lote_id')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', (string) $request->string('estado')))
            ->when($request->filled('origen'), fn ($q) => $q->where('origen', (string) $request->string('origen')))
            ->when($request->filled('score_vigor_min'), fn ($q) => $q->where('score_vigor_actual', '>=', $request->integer('score_vigor_min')))
            ->when($request->filled('score_sanidad_min'), fn ($q) => $q->where('score_sanidad_actual', '>=', $request->integer('score_sanidad_min')))
            ->when($request->filled('tasa_min'), fn ($q) => $q->whereNotNull('tasa_crecimiento')->where('tasa_crecimiento', '>=', $request->float('tasa_min')))
            ->when($request->filled('esbeltez_min'), fn ($q) => $q->whereNotNull('indice_esbeltez')->where('indice_esbeltez', '>=', $request->float('esbeltez_min')))
            ->when($request->filled('esbeltez_max'), fn ($q) => $q->whereNotNull('indice_esbeltez')->where('indice_esbeltez', '<=', $request->float('esbeltez_max')))
            ->when($request->filled('max_fitosanitarios'), fn ($q) => $q->where('n_eventos_fitosanitarios', '<=', $request->integer('max_fitosanitarios')));

        $weights = [
            'crecimiento' => (int) ($filters['peso_crecimiento'] ?? 40),
            'esbeltez' => (int) ($filters['peso_esbeltez'] ?? 20),
            'vigor' => (int) ($filters['peso_vigor'] ?? 20),
            'sanidad' => (int) ($filters['peso_sanidad'] ?? 20),
        ];

        $selection = [
            'esbeltez_min' => (float) ($filters['esbeltez_min'] ?? 0.8),
            'esbeltez_max' => (float) ($filters['esbeltez_max'] ?? 1.8),
            'crecimiento_ref' => (float) ($filters['crecimiento_ref'] ?? 1.0),
        ];

        $rows = $query->get()->map(function (Planta $planta) use ($weights, $selection) {
            $indice = $this->indiceDesempeno($planta, $weights, $selection);

            return [
                'planta' => $planta,
                'indice_desempeno' => $indice,
            ];
        });

        $sort = $filters['sort'] ?? 'score_desempeno';
        $direction = $filters['direction'] ?? 'desc';

        $rows = $rows->sortBy(function (array $row) use ($sort) {
            return match ($sort) {
                'score_desempeno' => $row['indice_desempeno'],
                'codigo' => $row['planta']->codigo,
                'tasa_crecimiento' => $row['planta']->tasa_crecimiento ?? -INF,
                'indice_esbeltez' => $row['planta']->indice_esbeltez ?? -INF,
                'score_vigor_actual' => $row['planta']->score_vigor_actual ?? -INF,
                'score_sanidad_actual' => $row['planta']->score_sanidad_actual ?? -INF,
                'n_eventos_fitosanitarios' => $row['planta']->n_eventos_fitosanitarios,
                default => $row['indice_desempeno'],
            };
        }, SORT_REGULAR, $direction === 'desc')->values();

        return [
            'plantas' => $rows,
            'filtros' => $filters,
            'weights' => $weights,
            'selection' => $selection,
            'variedades' => \App\Models\Variedad::orderBy('nombre')->get(),
            'lotes' => \App\Models\Lote::orderBy('nombre')->get(),
        ];
    }

    private function indiceDesempeno(Planta $planta, array $weights, array $selection): float
    {
        $totalPesos = max(array_sum($weights), 1);

        $crecimiento = $planta->tasa_crecimiento === null
            ? 0
            : min(100, max(0, ($planta->tasa_crecimiento / $selection['crecimiento_ref']) * 100));

        $esbeltez = $planta->indice_esbeltez === null
            ? 0
            : (
                $planta->indice_esbeltez >= $selection['esbeltez_min'] && $planta->indice_esbeltez <= $selection['esbeltez_max']
                    ? 100
                    : 0
            );

        $vigor = $planta->score_vigor_actual === null ? 0 : ($planta->score_vigor_actual / 5) * 100;
        $sanidad = $planta->score_sanidad_actual === null ? 0 : ($planta->score_sanidad_actual / 5) * 100;

        return round(
            (($crecimiento * $weights['crecimiento'])
            + ($esbeltez * $weights['esbeltez'])
            + ($vigor * $weights['vigor'])
            + ($sanidad * $weights['sanidad'])) / $totalPesos,
            2
        );
    }
}
