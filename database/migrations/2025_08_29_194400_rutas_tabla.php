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
            $table->decimal('distancia_trayectos_km', 10, 2);
            $table->decimal('distancia_total_km', 10, 2);

            $table->string('ruta_salida')->nullable();
            $table->string('ruta_llegada')->nullable();

            $table->foreignId('programacion_id')->constrained('programaciones')->onDelete('cascade');
            
            // Auditoría
            $table->timestamp('fechacreacion')->useCurrent();
            $table->timestamp('fechamodificacion')->useCurrent()->useCurrentOnUpdate();

            // estos conviene dejarlos nullables si no los vas a poner tú al insertar
            $table->unsignedBigInteger('usuariocreacion')->nullable();
            $table->unsignedBigInteger('usuariomodificacion')->nullable();
            $table->ipAddress('ipcreacion')->nullable();
            $table->ipAddress('ipmodificacion')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        {
            Schema::dropIfExists('rutas');
        }
    }
};
