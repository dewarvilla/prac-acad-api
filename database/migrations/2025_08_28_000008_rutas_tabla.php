<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('programacion_id')
                  ->constrained('programaciones')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->decimal('origen_lat', 10, 7)->nullable();
            $table->decimal('origen_lng', 10, 7)->nullable();
            $table->string('origen_desc')->nullable();
            $table->string('origen_place_id')->nullable();

            $table->decimal('destino_lat', 10, 7)->nullable();
            $table->decimal('destino_lng', 10, 7)->nullable();
            $table->string('destino_desc')->nullable();
            $table->string('destino_place_id')->nullable();

            $table->unsignedBigInteger('distancia_m')->nullable();     
            $table->unsignedInteger('duracion_s')->nullable();        
            $table->text('polyline')->nullable();                         

            $table->unsignedInteger('numero_peajes')->nullable();
            $table->decimal('valor_peajes', 12, 2)->nullable();

            $table->unsignedInteger('orden')->default(1);

            $table->text('justificacion');

            // Auditoría
            $table->boolean('estado')->default(true);
            $table->timestamp('fechacreacion')->useCurrent();
            $table->timestamp('fechamodificacion')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('usuariocreacion')->nullable();
            $table->unsignedBigInteger('usuariomodificacion')->nullable();
            $table->ipAddress('ipcreacion')->nullable();
            $table->ipAddress('ipmodificacion')->nullable();

            // Índices útiles
            $table->index(['programacion_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
