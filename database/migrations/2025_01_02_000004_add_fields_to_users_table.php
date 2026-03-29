<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('role_id')->constrained('areas')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->after('area_id')->constrained('sucursales')->nullOnDelete();
            $table->string('cargo')->nullable()->after('name');
            $table->string('telefono', 20)->nullable()->after('cargo');
            $table->boolean('activo')->default(true)->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['area_id']);
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['role_id', 'area_id', 'sucursal_id', 'cargo', 'telefono', 'activo']);
        });
    }
};
