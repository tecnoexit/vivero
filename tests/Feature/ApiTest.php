<?php

namespace Tests\Feature;

use App\Models\Planta;
use App\Models\Variedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_catalogs_as_json(): void
    {
        Variedad::create(['nombre' => 'Caturra', 'especie' => 'Coffea arabica']);

        $response = $this->getJson('/api/catalogos/variedades');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.nombre', 'Caturra');
    }

    public function test_can_show_plant_with_timeline_as_json(): void
    {
        $variedad = Variedad::create(['nombre' => 'Bourbon']);
        $planta = Planta::create([
            'codigo' => 'BOU-0001',
            'variedad_id' => $variedad->id,
            'origen' => 'propia',
            'fecha_alta' => now(),
            'contenedor' => 'maceta',
            'estado' => 'activa',
            'publico_activo' => true,
        ]);

        $response = $this->getJson('/api/plantas/'.$planta->id);

        $response->assertOk();
        $response->assertJsonPath('data.codigo', 'BOU-0001');
        $response->assertJsonStructure(['data', 'timeline']);
    }
}
