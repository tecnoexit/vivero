<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_evento_id')->constrained('tipo_eventos')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('producto')->nullable();
            $table->string('dosis')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('evento_planta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('planta_id')->constrained('plantas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['evento_id', 'planta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_planta');
        Schema::dropIfExists('eventos');
    }
};
