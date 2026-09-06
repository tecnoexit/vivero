<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bandejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variedad_id')->constrained('variedades')->cascadeOnDelete();
            $table->string('origen');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->date('fecha_siembra')->nullable();
            $table->unsignedInteger('n_semillas')->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandejas');
    }
};
