<?php

namespace Tests\Feature;

use App\Models\Bandeja;
use App\Models\EtapaFenologica;
use App\Models\Lote;
use App\Models\Planta;
use App\Models\TipoEvento;
use App\Models\TipoFoto;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_operario_can_store_measurement_and_update_plant(): void
    {
        $user = User::factory()->create([
            'rol' => 'operario',
            'password' => Hash::make('password'),
        ]);

        $planta = $this->createPlant();

        $response = $this->actingAs($user)->post(route('captura.store', 'mediciones'), [
            'planta_id' => $planta->id,
            'fecha' => now()->toDateString(),
            'altura_cm' => 12,
            'diametro_tallo_mm' => 3,
        ]);

        $response->assertRedirect(route('plantas.show', $planta));

        $planta->refresh();
        $this->assertSame(12.0, (float) $planta->ultima_altura);
        $this->assertSame(4.0, (float) $planta->indice_esbeltez);
    }

    public function test_operario_can_store_event_for_multiple_plants(): void
    {
        $user = User::factory()->create([
            'rol' => 'operario',
            'password' => Hash::make('password'),
        ]);

        $plantaA = $this->createPlant('CAT-0001');
        $plantaB = $this->createPlant('CAT-0002');
        $tipo = TipoEvento::create(['nombre' => 'Fitosanitario']);

        $response = $this->actingAs($user)->post(route('captura.store', 'eventos'), [
            'tipo_evento_id' => $tipo->id,
            'fecha' => now()->toDateString(),
            'producto' => 'Producto X',
            'dosis' => '10 ml',
            'plantas_ids' => [$plantaA->id, $plantaB->id],
        ]);

        $response->assertRedirect(route('plantas.show', $plantaA));
        $this->assertDatabaseHas('evento_planta', [
            'planta_id' => $plantaA->id,
        ]);
        $this->assertDatabaseHas('evento_planta', [
            'planta_id' => $plantaB->id,
        ]);
    }

    public function test_admin_can_store_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'rol' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $planta = $this->createPlant();
        $tipo = TipoFoto::create(['nombre' => 'General']);

        $response = $this->actingAs($user)->post(route('captura.store', 'fotos'), [
            'planta_id' => $planta->id,
            'tipo_foto_id' => $tipo->id,
            'fecha' => now()->toDateString(),
            'imagen' => UploadedFile::fake()->create('planta.jpg', 200, 'image/jpeg'),
            'activa' => 1,
        ]);

        $response->assertRedirect(route('plantas.show', $planta));
        $this->assertDatabaseHas('fotos', ['planta_id' => $planta->id]);
        $foto = \App\Models\Foto::first();
        Storage::disk('public')->assertExists($foto->imagen);
    }

    public function test_operario_can_store_evaluation(): void
    {
        $user = User::factory()->create([
            'rol' => 'operario',
            'password' => Hash::make('password'),
        ]);

        $planta = $this->createPlant();

        $response = $this->actingAs($user)->post(route('captura.store', 'evaluaciones'), [
            'planta_id' => $planta->id,
            'fecha' => now()->toDateString(),
            'score_vigor' => 4,
            'score_sanidad' => 5,
        ]);

        $response->assertRedirect(route('plantas.show', $planta));
        $planta->refresh();
        $this->assertSame(4, (int) $planta->score_vigor_actual);
        $this->assertSame(5, (int) $planta->score_sanidad_actual);
    }

    private function createPlant(string $codigo = 'CAT-0001'): Planta
    {
        $variedad = Variedad::firstOrCreate(['nombre' => 'Caturra'], ['especie' => 'Coffea arabica']);
        $etapa = EtapaFenologica::firstOrCreate(['nombre' => 'Vegetativa'], ['orden' => 1]);
        $lote = Lote::firstOrCreate(['nombre' => 'Lote A'], ['ubicacion' => 'Sector norte', 'tipo' => 'invernadero']);
        $bandeja = Bandeja::firstOrCreate(['variedad_id' => $variedad->id, 'origen' => 'propia', 'n_semillas' => 10]);

        return Planta::create([
            'codigo' => $codigo,
            'variedad_id' => $variedad->id,
            'origen' => 'propia',
            'bandeja_id' => $bandeja->id,
            'fecha_alta' => now()->subDays(7),
            'etapa_fenologica_id' => $etapa->id,
            'contenedor' => 'maceta',
            'lote_id' => $lote->id,
            'estado' => 'activa',
            'publico_activo' => true,
        ]);
    }
}
