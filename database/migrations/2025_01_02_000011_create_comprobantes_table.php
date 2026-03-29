<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ejecucion_id')->constrained('ejecuciones')->cascadeOnDelete();
            $table->enum('tipo', ['factura', 'recibo', 'nota_fiscal', 'comprobante_pago', 'otro']);
            $table->string('numero', 50)->nullable();
            $table->string('archivo');
            $table->string('nombre_original');
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 18, 2)->nullable();
            $table->date('fecha_documento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
