<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios_atencion', function (Blueprint $table) {
            // null = horario general del negocio (como hasta ahora).
            // con valor = horario propio de ESE profesional para ese día, tiene prioridad.
            $table->foreignUuid('personal_id')->nullable()->after('negocio_id')
                ->constrained('personal')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('horarios_atencion', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
            $table->dropColumn('personal_id');
        });
    }
};
