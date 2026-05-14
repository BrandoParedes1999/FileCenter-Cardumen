<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('registro_de_actividad', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->unsignedInteger('usuario_id')->nullable()->change();
            $table->foreign('usuario_id', 'fk_log_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::table('registro_de_actividad', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->unsignedInteger('usuario_id')->nullable(false)->change();
            $table->foreign('usuario_id', 'fk_log_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }
};
