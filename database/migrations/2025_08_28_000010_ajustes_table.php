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
        // tabla ajuste de solicitudes de programacion de practicas
        Schema::create('ajustes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('programacion_id');
            
            $table->date('fecha_ajuste');

            $table->enum('estado_ajuste', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_vice', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_jefe_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_coordinardor_postg', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->string('justificacion');

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
            Schema::dropIfExists('ajustes');
        }
    }
};
