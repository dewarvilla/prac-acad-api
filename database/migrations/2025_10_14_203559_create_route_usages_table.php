<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_usages', function (Blueprint $table) {
            $table->id();
            // Clave de mes en formato YYYY-MM (ej. 2025-10)
            $table->string('month_key', 7)->unique();
            // Conteo realizado (requests ya reservadas)
            $table->unsignedInteger('count')->default(0);
            // Límite y umbral de aviso "fotografiados" al crear el registro del mes (opcional)
            $table->unsignedInteger('limit')->default(0);
            $table->decimal('warn_ratio', 4, 2)->default(0.80); // 0.00 - 1.00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_usages');
    }
};
