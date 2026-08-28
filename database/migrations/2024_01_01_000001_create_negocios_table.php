<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->string('tipo_negocio')->nullable(); // ej. "barbería", "restaurante" — solo informativo
            $table->string('telefono_whatsapp')->nullable();
            $table->string('direccion')->nullable();
            $table->string('color_marca')->nullable(); // hex, para personalizar la UI por negocio
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocios');
    }
};
