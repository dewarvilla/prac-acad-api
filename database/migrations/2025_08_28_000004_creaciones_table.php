<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('catalogo_id')
                  ->constrained('catalogos')     
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();          

            $table->string('nombre_practica');
            $table->text('recursos_necesarios');
            $table->text('justificacion');

            $table->enum('estado_practica', ['en_aprobacion', 'aprobada', 'creada'])
                  ->default('en_aprobacion');

            $table->enum('nivel_academico', ['pregrado', 'postgrado'])->default('pregrado');

            $table->string('facultad');
            $table->string('programa_academico');

            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_facultad', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_academico', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->unique(['catalogo_id','nombre_practica'], 'creaciones_catalogo_nombre_unique');

            $table->index('programa_academico');   
            $table->index('nombre_practica');    
            $table->index('estado_practica');     
            $table->index('facultad');    

            // Auditoría
            $table->timestamp('fechacreacion')->useCurrent();
            $table->timestamp('fechamodificacion')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('usuariocreacion')->nullable();
            $table->unsignedBigInteger('usuariomodificacion')->nullable();
            $table->ipAddress('ipcreacion')->nullable();
            $table->ipAddress('ipmodificacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creaciones');
    }
};

