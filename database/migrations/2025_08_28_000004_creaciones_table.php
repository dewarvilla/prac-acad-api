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

            // FK con RESTRICT (no permite borrar catálogos referenciados)
            $table->foreignId('catalogo_id')
                  ->constrained('catalogos')      // references('id')->on('catalogos')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();           // <- clave

            $table->string('nombre_practica');
            $table->text('recursos_necesarios');
            $table->text('justificacion');

            $table->enum('estado_practica', ['en_aprobacion', 'aprobada', 'creada'])
                  ->default('en_aprobacion');

            // Se llena desde Catalogo en el controller; déjalo nullable
            $table->string('nivel_academico')->nullable();

            // Se copian desde Catalogo en el controller
            $table->string('facultad');
            $table->string('programa_academico');

            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_facultad', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_consejo_academico', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            // Índice único con nombre estable (elimina el duplicado que tenías)
            $table->unique(['nombre_practica', 'programa_academico'], 'creaciones_nombre_programa_unique');

            // Índices (para búsquedas/orden)
            $table->index('programa_academico');   // ordenar/filtrar por programa
            $table->index('nombre_practica');      // buscar por nombre
            $table->index('estado_practica');      // filtrar por estado
            $table->index('facultad');             // si buscas por facultad

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

