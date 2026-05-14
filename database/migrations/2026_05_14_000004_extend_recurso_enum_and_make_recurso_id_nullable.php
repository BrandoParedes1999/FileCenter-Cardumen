<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega 'sistema' al ENUM recurso y hace recurso_id nullable.
     * Permite registrar eventos del sistema sin un recurso específico.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("
                ALTER TABLE registro_de_actividad
                MODIFY COLUMN recurso ENUM(
                    'archivo',
                    'carpeta',
                    'solicitud',
                    'usuario',
                    'version',
                    'sistema'
                ) NOT NULL
            ");

            DB::statement("
                ALTER TABLE registro_de_actividad
                MODIFY COLUMN recurso_id INT UNSIGNED NULL
            ");
        } else {
            // SQLite: recurso_id nullable (ENUM es string en SQLite)
            Schema::table('registro_de_actividad', function (Blueprint $table) {
                $table->unsignedInteger('recurso_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("
                ALTER TABLE registro_de_actividad
                MODIFY COLUMN recurso ENUM(
                    'archivo',
                    'carpeta',
                    'solicitud',
                    'usuario',
                    'version'
                ) NOT NULL
            ");

            DB::statement("
                ALTER TABLE registro_de_actividad
                MODIFY COLUMN recurso_id INT UNSIGNED NOT NULL
            ");
        } else {
            Schema::table('registro_de_actividad', function (Blueprint $table) {
                $table->unsignedInteger('recurso_id')->nullable(false)->change();
            });
        }
    }
};
