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
        // tabla legalizacion de practicas
        Schema::create('legalizaciones', function (Blueprint $table) {
            $table->id();

            $table->date('fecha_legalizacion');

            $table->enum('estado_depart', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_postg', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_tesoreria', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            $table->enum('estado_contabilidad', ['aprobada', 'rechazada', 'pendiente'])->default('pendiente');
            
            $table->foreignId('practica_id')->constrained('practicas')->onDelete('cascade');

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
