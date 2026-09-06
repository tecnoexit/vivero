    */
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mediciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planta_id')->constrained('plantas')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('altura_cm', 8, 2)->nullable();
            $table->decimal('diametro_tallo_mm', 8, 2)->nullable();
            $table->decimal('longitud_hoja_cm', 8, 2)->nullable();
            $table->decimal('diametro_copa_cm', 8, 2)->nullable();
            $table->unsignedInteger('n_ramas')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediciones');
    }
};
