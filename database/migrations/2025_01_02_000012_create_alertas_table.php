<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['presupuesto_bajo', 'solicitud_pendiente', 'gasto_excedido', 'duplicidad', 'limite_mensual']);
            $table->string('titulo');
            $table->text('mensaje');
            $table->enum('nivel', ['info', 'warning', 'danger'])->default('info');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
