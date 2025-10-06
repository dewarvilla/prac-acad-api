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
        Schema::create('catalogos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('nivel_academico', ['pregrado', 'postgrado'])->default('pregrado');
            $table->string('facultad');
            $table->string('programa_academico');

            // Único con nombre explícito (evita duplicados/confusión en down/alter)
            $table->unique(['programa_academico', 'facultad'], 'catalogos_programa_facultad_unique');

            // Índices para filtrar/ordenar rápido
            $table->index('facultad', 'catalogos_facultad_idx');
            $table->index('programa_academico', 'catalogos_programa_idx');

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
            Schema::dropIfExists('catalogos');
        }
    }
};
