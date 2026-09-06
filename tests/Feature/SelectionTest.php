<?php

namespace Tests\Feature;

use App\Models\Bandeja;
use App\Models\EtapaFenologica;
use App\Models\Evaluacion;
use App\Models\Lote;
use App\Models\Medicion;
use App\Models\Planta;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_and_rank_plants(): void
    {
        $admin = User::factory()->create([
            'rol' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $variedad = Variedad::create(['nombre' => 'Caturra', 'especie' => 'Coffea arabica']);
        $etapa = EtapaFenologica::create(['nombre' => 'Vegetativa', 'orden' => 1]);
        $lote = Lote::create(['nombre' => 'Lote A', 'ubicacion' => 'Sector norte', 'tipo' => 'invernadero']);
        $bandeja = Bandeja::create(['variedad_id' => $variedad->id, 'origen' => 'propia', 'n_semillas' => 10]);

        $ganadora = Planta::create([
            'variedad_id' => $variedad->id,
            'origen' => 'propia',
            'bandeja_id' => $bandeja->id,
            'fecha_alta' => now()->subDays(10),
            'etapa_fenologica_id' => $etapa->id,
            'contenedor' => 'maceta',
            'lote_id' => $lote->id,
            'estado' => 'activa',
            'publico_activo' => true,
        ]);

        Medicion::create(['planta_id' => $ganadora->id, 'fecha' => now()->subDays(10), 'altura_cm' => 10, 'diametro_tallo_mm' => 2]);
        Medicion::create(['planta_id' => $ganadora->id, 'fecha' => now()->subDays(3), 'altura_cm' => 18, 'diametro_tallo_mm' => 3]);
        Evaluacion::create(['planta_id' => $ganadora->id, 'fecha' => now()->subDay(), 'score_vigor' => 5, 'score_sanidad' => 5]);

        $debiles = Planta::create([
            'variedad_id' => $variedad->id,
            'origen' => 'propia',
            'fecha_alta' => now()->subDays(10),
            'etapa_fenologica_id' => $etapa->id,
            'contenedor' => 'maceta',
            'lote_id' => $lote->id,
            'estado' => 'activa',
            'publico_activo' => true,
        ]);

        Medicion::create(['planta_id' => $debiles->id, 'fecha' => now()->subDays(10), 'altura_cm' => 8, 'diametro_tallo_mm' => 2]);
        Medicion::create(['planta_id' => $debiles->id, 'fecha' => now()->subDays(3), 'altura_cm' => 9, 'diametro_tallo_mm' => 2]);
        Evaluacion::create(['planta_id' => $debiles->id, 'fecha' => now()->subDay(), 'score_vigor' => 2, 'score_sanidad' => 2]);

        $response = $this->actingAs($admin)->get(route('seleccion.index', ['score_vigor_min' => 4]));

        $response->assertOk();
        $response->assertSee($ganadora->codigo);
        $response->assertDontSee($debiles->codigo);
    }

    public function test_admin_can_export_selection_csv(): void
    {
        $admin = User::factory()->create([
            'rol' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $variedad = Variedad::create(['nombre' => 'Bourbon']);
        $planta = Planta::create([
            'codigo' => 'BOU-0001',
            'variedad_id' => $variedad->id,
            'origen' => 'propia',
            'fecha_alta' => now()->subDays(10),
            'contenedor' => 'maceta',
            'estado' => 'activa',
            'publico_activo' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('seleccion.export', ['variedad_id' => $variedad->id]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertDownload('seleccion.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('codigo', $content);
        $this->assertStringContainsString($planta->codigo, $content);
    }
}
