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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('catalogo_id');
            $table->string('nombre_practica');
            $table->text('recursos_necesarios');
            $table->text('justificacion');
            $table->enum('estado_practica', ['en_aprobacion', 'aprobada', 'creada'])->default('en_aprobacion');
            $table->string('nivel_academico')->nullable();
            $table->string('facultad');
            $table->string('programa_academico');
            
            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_facultad', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_academico', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->unique(['nombre_practica', 'programa_academico']);

            $table->foreign('catalogo_id')->references('id')->on('catalogos')->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['nombre_practica', 'programa_academico'], 'creaciones_nombre_programa_unique');

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
            Schema::dropIfExists('creaciones');
        }
    }
};
