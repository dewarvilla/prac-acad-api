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
        // tabla legalizacion de practicas
        Schema::create('legalizaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('programacion_id');
            $table->date('fecha_legalizacion');
            
            $table->enum('estado_legalizacion', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_postg', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_logistica', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_tesoreria', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_contabilidad', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            
            $table->foreign('programacion_id')->references('id')->on('programaciones')->onUpdate('cascade')->onDelete('cascade');

            // Auditoría
            $table->timestamp('fechacreacion')->useCurrent();
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
            Schema::dropIfExists('legalizaciones');
        }
    }
};
