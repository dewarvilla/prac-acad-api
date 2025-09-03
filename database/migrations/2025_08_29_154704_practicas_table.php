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
        //tabla crear practicas
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->enum('nivel', ['pregrado', 'posgrado']);
            $table->string('facultad');
            $table->string('programa_academico');
            $table->text('descripcion');
            $table->string('lugar_de_realizacion')->nullable();
            $table->text('justificacion');
            $table->text('recursos_necesarios');

            $table->enum('estado_practica', ['en_aprobacion', 'aprobada', 'rechazada', 'en_ejecucion',
            'ejecutada', 'en_legalizacion', 'legalizada' ])->default('en_aprobacion');
            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_postg', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_decano', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_jefe_postg', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_vice', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->date('fecha_inicio');
            $table->date('fecha_finalizacion');
            $table->date('fecha_solicitud');    

            $table->unique(['nombre', 'programa_academico']);//para no repetir el nombre de la practica en el mismo programa academico

            //Datos de auditoria
            $table->timestamp('fechacreacion');
            $table->integer('usuariocreacion');
            $table->timestamp('fechamodificacion');
            $table->integer('usuariomodificacion');
            $table->string('ipcreacion',255);
            $table->string('ipmodificacion',255);
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
