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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('programacion_id');

            $table->string('latitud_salidas');
            $table->string('latitud_llegadas');
            $table->integer('numero_recorridos');
            $table->integer('numero_peajes');

            $table->decimal('valor_peajes', 10, 2);
            $table->decimal('distancia_trayectos_km', 10, 2);

            $table->string('ruta_salida')->nullable();
            $table->string('ruta_llegada')->nullable();

            $table->foreign('programacion_id')->references('id')->on('programaciones')->onUpdate('cascade')->onDelete('cascade');
            
            // Auditoría
            $table->boolean('estado')->default(true)->comment('');
            $table->timestamp('fogramacionechacreacion')->useCurrent();
            $table->timestamp('fechamodificacion')->useCurrent()->useCurrentOnUpdate();
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
