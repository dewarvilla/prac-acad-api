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
        //tabla para crear practicas por el vice academico
        Schema::create('creaciones', function (Blueprint $table) {
            $table->id();
            
            $table->enum('nivel_academico', ['pregrado', 'postgrado'])->default('pregrado');
            $table->string('facultad');
            $table->string('programa_academico');
            $table->string('nombre_practica');
            $table->text('recursos_necesarios');
            $table->text('justificacion');
            $table->enum('estado_practica', ['en_aprobacion', 'aprobada', 'creada'])->default('en_aprobacion');
            
            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_facultad', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_academico', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->unique(['nombre', 'programa_academico']);//para no repetir el nombre de la practica en el mismo programa academico

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
