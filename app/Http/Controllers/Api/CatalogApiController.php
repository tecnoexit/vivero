<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CatalogApiController extends Controller
{
    public function index(string $resource): JsonResponse
    {
        $config = config("nursery.resources.$resource");

        abort_unless($config, 404);

        $model = $config['model'];

        return response()->json([
            'data' => $model::orderByDesc('id')->get(),
        ]);
    }

    public function show(string $resource, int $id): JsonResponse
    {
        $config = config("nursery.resources.$resource");

        abort_unless($config, 404);

        $model = $config['model'];

        return response()->json([
            'data' => $model::findOrFail($id),
        ]);
    }
}
