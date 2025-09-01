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
            $table->string('apellido');
            $table->string('correo_institucional')->nullable();
            $table->string('telefono');
            $table->string('programa_academico')->nullable();
            $table->string('facultad')->nullable();
            $table->boolean('repitente')->default(false);
            
            $table->enum('tipo_participante', ['estudiante', 'docente', 'acompanante']);
            
            $table->foreignId('practica_id')->constrained('practicas')->onDelete('cascade');

            //para que no se repita el participante en la misma practica
            $table->unique(['numero_identificacion', 'practica_id']);

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
