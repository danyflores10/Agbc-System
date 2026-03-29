<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestion_id')->constrained('gestiones')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('partida_id')->constrained('partidas_presupuestarias')->cascadeOnDelete();
            $table->unsignedTinyInteger('mes')->nullable()->comment('1-12, null para anual');
            $table->decimal('monto_asignado', 18, 2)->default(0);
            $table->decimal('monto_ejecutado', 18, 2)->default(0);
            $table->decimal('monto_comprometido', 18, 2)->default(0);
            $table->enum('estado', ['borrador', 'aprobado', 'ejecutando', 'cerrado'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['gestion_id', 'area_id', 'sucursal_id', 'partida_id', 'mes'], 'presupuesto_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
