<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_gasto', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique();
            $table->foreignId('gestion_id')->constrained('gestiones')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('partida_id')->constrained('partidas_presupuestarias')->cascadeOnDelete();
            $table->foreignId('presupuesto_id')->nullable()->constrained('presupuestos')->nullOnDelete();
            $table->decimal('monto_solicitado', 18, 2);
            $table->decimal('monto_aprobado', 18, 2)->nullable();
            $table->string('concepto');
            $table->text('justificacion');
            $table->string('beneficiario')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estado', ['pendiente', 'en_revision', 'aprobado', 'rechazado', 'ejecutado', 'anulado'])->default('pendiente');
            $table->text('motivo_rechazo')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_gasto');
    }
};
