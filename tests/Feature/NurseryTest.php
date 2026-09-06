<?php

namespace Tests\Feature;

use App\Models\Bandeja;
use App\Models\EtapaFenologica;
use App\Models\Lote;
use App\Models\Medicion;
use App\Models\Planta;
use App\Models\TipoEvento;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NurseryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'rol' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
    }

    public function test_planta_code_is_generated_and_metric_is_updated(): void
    {
        $variedad = Variedad::create(['nombre' => 'Caturra', 'especie' => 'Coffea arabica']);
        $etapa = EtapaFenologica::create(['nombre' => 'Vegetativa', 'orden' => 1]);
        $lote = Lote::create(['nombre' => 'Lote A', 'ubicacion' => 'Sector norte', 'tipo' => 'invernadero']);
        $bandeja = Bandeja::create(['variedad_id' => $variedad->id, 'origen' => 'propia', 'n_semillas' => 10]);

        $planta = Planta::create([
            'variedad_id' => $variedad->id,
            'origen' => 'propia',
            'bandeja_id' => $bandeja->id,
            'fecha_alta' => now(),
            'etapa_fenologica_id' => $etapa->id,
            'contenedor' => 'maceta',
            'lote_id' => $lote->id,
            'estado' => 'activa',
            'publico_activo' => true,
        ]);

        $this->assertNotEmpty($planta->codigo);

        Medicion::create([
            'planta_id' => $planta->id,
            'fecha' => now()->subDays(7),
            'altura_cm' => 10,
            'diametro_tallo_mm' => 2,
        ]);

        $planta->refresh();

        $this->assertSame(10.0, (float) $planta->ultima_altura);
        $this->assertSame(5.0, (float) $planta->indice_esbeltez);
    }

    public function test_public_token_route_is_available(): void
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

        $response = $this->get(route('public.planta', $planta->token_publico));

        $response->assertOk();
        $response->assertSee('BOU-0001');
    }
}
