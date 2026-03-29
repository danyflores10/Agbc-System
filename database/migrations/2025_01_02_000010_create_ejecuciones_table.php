<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejecuciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_gasto')->cascadeOnDelete();
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->cascadeOnDelete();
            $table->decimal('monto_ejecutado', 18, 2);
            $table->date('fecha_ejecucion');
            $table->string('concepto');
            $table->string('beneficiario')->nullable();
            $table->string('nro_documento', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejecuciones');
    }
};
