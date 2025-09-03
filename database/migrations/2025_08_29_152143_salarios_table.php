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
        //tabla SMMLV
        Schema::create('salarios', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedInteger('anio')->unique();
            $table->decimal('valor', 12, 2);

            //Datos de auditoria
            $table->timestamp('fechacreacion');
            $table->integer('usuariocreacion');
            $table->timestamp('fechamodificacion');
            $table->integer('usuariomodificacion');
            $table->string('ipcreacion',255);
            $table->string('ipmodificacion',255);

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
