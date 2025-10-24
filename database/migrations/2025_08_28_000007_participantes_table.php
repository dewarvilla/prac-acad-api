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
            $table->bigIncrements('id');
            $table->string('numero_identificacion');
            $table->boolean('discapacidad')->default(false);
            $table->string('nombre');
            $table->string('correo_institucional')->nullable();
            $table->string('telefono');
            $table->string('programa_academico')->nullable();
            $table->string('facultad')->nullable();
            $table->boolean('repitente')->default(false);
            $table->enum('tipo_participante', ['estudiante', 'docente', 'acompanante']);
            
            $table->foreignId('programacion_id')->constrained('programaciones')->cascadeOnUpdate()->restrictOnDelete();  
            $table->unique(['numero_identificacion', 'programacion_id']);

            // Auditoría
            $table->boolean('estado')->default(true)->comment('');
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
            Schema::dropIfExists('participantes');
        }
    }
};
