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
        //tabla participantes de practicas
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();

            $table->string('numero_identificacion');
            $table->boolean('discapacidad')->default(false);
            $table->string('nombre');
            $table->string('correo_institucional')->nullable();
            $table->string('telefono');
            $table->string('programa_academico')->nullable();
            $table->string('facultad')->nullable();
            $table->boolean('repitente')->default(false);
            $table->enum('tipo_participante', ['estudiante', 'docente', 'acompanante']);
            
            $table->foreignId('programacion_id')->constrained('programaciones')->onDelete('cascade');

            //para que no se repita el participante en la misma practica
            $table->unique(['numero_identificacion', 'programacion_id']);

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
