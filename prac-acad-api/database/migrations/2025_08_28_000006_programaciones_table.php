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
        //tabla para programaciones de practicas
        Schema::create('programaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('creacion_id');
            $table->string('nombre_practica');
            $table->text('descripcion');
            $table->boolean('requiere_transporte')->default(false);
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

            $table->foreign('creacion_id')->references('id')->on('creaciones')->onUpdate('cascade')->onDelete('cascade');

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
            Schema::dropIfExists('programaciones');
        }
    }
};
