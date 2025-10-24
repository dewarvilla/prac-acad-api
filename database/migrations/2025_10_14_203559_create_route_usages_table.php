<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_usages', function (Blueprint $table) {
            $table->id();
            $table->string('month_key', 7)->unique();
            $table->unsignedInteger('count')->default(0);
            $table->unsignedInteger('limit')->default(0);
            $table->decimal('warn_ratio', 4, 2)->default(0.80);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_usages');
    }
};
