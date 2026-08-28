<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('negocio_id')->constrained('negocios')->cascadeOnDelete();
            $table->foreignUuid('servicio_id')->constrained('servicios');
            $table->foreignUuid('personal_id')->nullable()->constrained('personal')->nullOnDelete();

            $table->date('fecha');
            $table->time('hora'); // el fin se calcula sumando servicio.duracion_minutos, no se guarda hora_fin

            $table->unsignedInteger('numero_personas')->nullable(); // ej. restaurante ("para cuántos")

            $table->string('nombre_cliente');
            $table->string('telefono_cliente');
            $table->string('email_cliente')->nullable();

            $table->string('estado')->default('pendiente'); // pendiente | confirmada | cancelada
            $table->string('token_gestion')->unique(); // arma el enlace de "gestionar mi reserva"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};
