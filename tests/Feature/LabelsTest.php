<?php

namespace Tests\Feature;

use App\Models\Variedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LabelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_label_generator(): void
    {
        $admin = User::factory()->create([
            'rol' => 'admin',
            'password' => Hash::make('password'),
        ]);

        Variedad::create(['nombre' => 'Caturra']);

        $response = $this->actingAs($admin)->get(route('etiquetas.create'));

        $response->assertOk();
        $response->assertSee('Generar etiquetas');
    }

    public function test_admin_can_generate_printable_labels_with_sequential_codes(): void
    {
        $admin = User::factory()->create([
            'rol' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $variedad = Variedad::create(['nombre' => 'Caturra']);

        $response = $this->actingAs($admin)->post(route('etiquetas.store'), [
            'variedad_id' => $variedad->id,
            'cantidad' => 2,
            'formats' => ['numero', 'qr'],
        ]);

        $response->assertOk();
        $response->assertSee('CAT-0001');
        $response->assertSee('CAT-0002');
        $response->assertSee('QR');
    }
}
