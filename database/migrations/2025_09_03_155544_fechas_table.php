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
        //tabla de fecha de apertura y cierre definida por el vice academica
        Schema::create('fechas', function (Blueprint $table) {
            $table->id();
            
            $table->date('fecha_apertura_preg')->unique();
            $table->date('fecha_cierre_docente_preg')->unique();
            $table->date('fecha_cierre_jefe_depart')->unique();
            $table->date('fecha_cierre_decano')->unique();
            $table->date('fecha_apertura_postg')->unique();
            $table->date('fecha_cierre_docente_postg')->unique();
            $table->date('fecha_cierre_coordinador_postg')->unique();
            $table->date('fecha_cierre_jefe_postg')->unique();

            // Auditoría
            $table->timestamp('fechacreacion')->useCurrent();
            $table->timestamp('fechamodificacion')->useCurrent()->useCurrentOnUpdate();

            // estos conviene dejarlos nullables si no los vas a poner tú al insertar
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
            Schema::dropIfExists('fechas');
        }
    }
};
