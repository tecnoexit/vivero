<?php

namespace Database\Seeders;

use App\Models\Bandeja;
use App\Models\EtapaFenologica;
use App\Models\Lote;
use App\Models\Planta;
use App\Models\Proveedor;
use App\Models\TipoEvento;
use App\Models\TipoFoto;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@vivero.test'],
            ['name' => 'Admin Vivero', 'rol' => 'admin', 'password' => Hash::make('password')]
        );

        User::firstOrCreate(
            ['email' => 'operario@vivero.test'],
            ['name' => 'Operario Vivero', 'rol' => 'operario', 'password' => Hash::make('password')]
        );

        $caturra = Variedad::firstOrCreate(['nombre' => 'Caturra'], ['especie' => 'Coffea arabica']);
        $bourbon = Variedad::firstOrCreate(['nombre' => 'Bourbon'], ['especie' => 'Coffea arabica']);

        $proveedor = Proveedor::firstOrCreate(['nombre' => 'Vivero del Sur'], ['contacto' => 'info@viverodelsur.test']);
        $etapa = EtapaFenologica::firstOrCreate(['nombre' => 'Vegetativa'], ['orden' => 1]);
        $lote = Lote::firstOrCreate(['nombre' => 'Lote A'], ['ubicacion' => 'Sector norte', 'tipo' => 'invernadero']);
        $bandeja = Bandeja::firstOrCreate([
            'variedad_id' => $caturra->id,
            'origen' => 'proveedor',
            'proveedor_id' => $proveedor->id,
        ], ['fecha_siembra' => now()->subDays(21), 'n_semillas' => 20]);

        TipoEvento::firstOrCreate(['nombre' => 'Fitosanitario']);
        TipoEvento::firstOrCreate(['nombre' => 'Trasplante']);
        TipoFoto::firstOrCreate(['nombre' => 'General']);
        TipoFoto::firstOrCreate(['nombre' => 'Hoja']);

        Planta::firstOrCreate(
            ['codigo' => 'CAT-0001'],
            [
                'variedad_id' => $caturra->id,
                'origen' => 'proveedor',
                'proveedor_id' => $proveedor->id,
                'bandeja_id' => $bandeja->id,
                'fecha_alta' => now()->subDays(14),
                'etapa_fenologica_id' => $etapa->id,
                'contenedor' => 'maceta',
                'lote_id' => $lote->id,
                'estado' => 'activa',
                'publico_activo' => true,
                'notas' => 'Planta de muestra.',
            ]
        );

        Planta::firstOrCreate(
            ['codigo' => 'BOU-0001'],
            [
                'variedad_id' => $bourbon->id,
                'origen' => 'propia',
                'fecha_alta' => now()->subDays(8),
                'etapa_fenologica_id' => $etapa->id,
                'contenedor' => 'maceta',
                'estado' => 'activa',
                'publico_activo' => true,
            ]
        );
    }
}
