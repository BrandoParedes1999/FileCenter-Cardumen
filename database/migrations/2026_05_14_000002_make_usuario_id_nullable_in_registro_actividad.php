<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite usuario_id = NULL en registro_de_actividad para logs de sistema
     * o contextos donde no hay usuario autenticado (e.g. tareas programadas).
     * El modelo PHP ya aceptaba null; esto sincroniza la restricción de BD.
     */
    public function up(): void
    {
        $fksExistentes = $this->obtenerFKsDeUsuarioId();

        Schema::table('registro_de_actividad', function (Blueprint $table) use ($fksExistentes) {
            foreach ($fksExistentes as $nombre) {
                $table->dropForeign($nombre);
            }
            $table->unsignedInteger('usuario_id')->nullable()->change();
            $table->foreign('usuario_id', 'fk_log_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        $fksExistentes = $this->obtenerFKsDeUsuarioId();

        Schema::table('registro_de_actividad', function (Blueprint $table) use ($fksExistentes) {
            foreach ($fksExistentes as $nombre) {
                $table->dropForeign($nombre);
            }
            $table->unsignedInteger('usuario_id')->nullable(false)->change();
            $table->foreign('usuario_id', 'fk_log_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    private function obtenerFKsDeUsuarioId(): array
    {
        $rows = DB::select("
            SELECT kcu.CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE kcu
            JOIN information_schema.TABLE_CONSTRAINTS tc
                ON  tc.CONSTRAINT_NAME  = kcu.CONSTRAINT_NAME
                AND tc.TABLE_SCHEMA     = kcu.TABLE_SCHEMA
                AND tc.TABLE_NAME       = kcu.TABLE_NAME
            WHERE kcu.TABLE_SCHEMA  = DATABASE()
              AND kcu.TABLE_NAME    = 'registro_de_actividad'
              AND kcu.COLUMN_NAME   = 'usuario_id'
              AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        return array_column($rows, 'CONSTRAINT_NAME');
    }
};
