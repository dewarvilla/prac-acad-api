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
            $table->id();
            
            $table->date('fecha_ajuste');

            $table->enum('estado_ajuste', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_vice', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_jefe_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_coordinardor_postg', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->string('justificacion');

            $table->foreignId('programacion_id')->constrained('programaciones')->onDelete('cascade');

            //Datos de auditoria
            $table->timestamp('fechacreacion');
            $table->unsignedBigInteger('usuariocreacion');
            $table->timestamp('fechamodificacion');
            $table->unsignedBigInteger('usuariomodificacion');
            $table->ipAddress('ipcreacion');
            $table->ipAddress('ipmodificacion');
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
