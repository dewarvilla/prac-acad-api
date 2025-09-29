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
        // tabla reprogramacion de practicas
        Schema::create('reprogramaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('programacion_id');
            
            $table->date('fecha_reprogramacion');

            $table->enum('estado_reprogramacion', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('tipo_reprogramacion', ['normal', 'emergencia'])->default('normal');
            $table->enum('estado_vice', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');

            $table->string('justificacion');

            $table->foreign('programacion_id')->references('id')->on('programaciones')->onUpdate('cascade')->onDelete('cascade');

            // Auditoría
            $table->timestamp('fechacreacion')->useCurrent();
            $table->timestamp('fechamodificacion')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('usuariocreacion')->nullable();
            $table->unsignedBigInteger('usuariomodificacion')->nullable();
            $table->ipAddress('ipcreacion')->nullable();
            $table->ipAddress('ipmodificacion')->nullable();

            $table->softDeletes(); // deleted_at
            $table->unsignedBigInteger('usuarioborrado')->nullable();
            $table->ipAddress('ipborrado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        {
            Schema::dropIfExists('reprogramaciones');
        }
    }
};
