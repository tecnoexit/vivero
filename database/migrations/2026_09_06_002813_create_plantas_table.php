<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable()->unique();
            $table->foreignId('variedad_id')->constrained('variedades')->cascadeOnDelete();
            $table->string('origen');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->foreignId('bandeja_id')->nullable()->constrained('bandejas')->nullOnDelete();
            $table->date('fecha_alta')->nullable();
            $table->foreignId('etapa_fenologica_id')->nullable()->constrained('etapa_fenologicas')->nullOnDelete();
            $table->string('contenedor');
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
            $table->string('estado')->default('activa');
            $table->date('fecha_baja')->nullable();
            $table->string('motivo_baja')->nullable();
            $table->text('notas')->nullable();
            $table->uuid('token_publico')->unique();
            $table->boolean('publico_activo')->default(true);
            $table->decimal('ultima_altura', 8, 2)->nullable();
            $table->decimal('ultimo_diametro', 8, 2)->nullable();
            $table->date('ultima_fecha_medicion')->nullable();
            $table->decimal('tasa_crecimiento', 8, 2)->nullable();
            $table->decimal('indice_esbeltez', 8, 2)->nullable();
            $table->unsignedInteger('n_eventos_fitosanitarios')->default(0);
            $table->unsignedTinyInteger('score_vigor_actual')->nullable();
            $table->unsignedTinyInteger('score_sanidad_actual')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantas');
    }
};
