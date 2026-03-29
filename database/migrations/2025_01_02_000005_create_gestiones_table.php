<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestiones', function (Blueprint $table) {
            $table->id();
            $table->integer('anio')->unique();
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('monto_total', 18, 2)->default(0);
            $table->enum('estado', ['borrador', 'activo', 'cerrado'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gestiones');
    }
};
