<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Agregar la restricción CHECK a nivel SQL
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_accepted_terms CHECK (accepted_terms = 1)");
    }

    public function down(): void {
        // Eliminar la restricción si existe (puede variar según el motor de base de datos)
        DB::statement("ALTER TABLE users DROP CONSTRAINT chk_accepted_terms");
    }
};

