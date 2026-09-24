<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // El id de esta tabla no tenía valor por defecto, así que sync()
        // fallaba al no poder generarlo automáticamente.
        DB::statement('ALTER TABLE personal_servicio ALTER COLUMN id SET DEFAULT gen_random_uuid()');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE personal_servicio ALTER COLUMN id DROP DEFAULT');
    }
};
