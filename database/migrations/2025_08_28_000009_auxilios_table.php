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
        //tabla auxilios de practicas
        Schema::create('auxilios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('programacion_id');
            $table->unsignedBigInteger('salario_id');
            
            $table->boolean('pernocta')->default(false);
            $table->boolean('distancias_mayor_70km')->default(false);
            $table->boolean('fuera_cordoba')->default(false);

            $table->integer('numero_total_estudiantes');
            $table->integer('numero_total_docentes');
            $table->integer('numero_total_acompanantes')->default(0);

            $table->decimal('valor_por_docente', 10, 2);
            $table->decimal('valor_por_estudiante', 10, 2);
            $table->decimal('valor_por_acompanante', 10, 2)->default(0);
            $table->decimal('valor_total_auxilio', 10, 2);

            $table->foreign('programacion_id')->references('id')->on('programaciones')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('salario_id')->references('id')->on('salarios')->onUpdate('cascade')->onDelete('cascade');

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
            Schema::dropIfExists('auxilios');
        }
    }
};
