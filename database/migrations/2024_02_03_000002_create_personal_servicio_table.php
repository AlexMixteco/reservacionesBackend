<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_servicio', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('personal_id')->constrained('personal')->cascadeOnDelete();
            $table->foreignUuid('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['personal_id', 'servicio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_servicio');
    }
};
