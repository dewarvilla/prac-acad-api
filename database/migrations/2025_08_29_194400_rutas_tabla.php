<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // tabla rutas de practicas
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();

            $table->string('latitud_salidas');
            $table->string('latitud_llegadas');
            $table->integer('numero_recorridos');
            $table->integer('numero_peajes');

            $table->decimal('valor_peajes', 10, 2);
            $table->decimal('valor_total_peajes', 10, 2);
            $table->decimal('distacia_trayectos_km', 10, 2);
            $table->decimal('distancia_total_km', 10, 2);

            $table->string('ruta_salida')->nullable();
            $table->string('ruta_llegada')->nullable();

            $table->foreignId('practica_id')->constrained('practicas')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
