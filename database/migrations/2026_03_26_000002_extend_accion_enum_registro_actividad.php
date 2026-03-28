<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Amplía el ENUM 'accion' en registro_de_actividad para incluir
     * las nuevas acciones de solicitudes de subida.
     *
     * NOTA: En MySQL, modificar un ENUM requiere redefinirlo completo.
     * En SQLite (dev), los ENUMs son strings, por lo que esto es un no-op.
     */
    public function up(): void
    {
        // Solo ejecutar en MySQL/MariaDB
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("
                ALTER TABLE registro_de_actividad
                MODIFY COLUMN accion ENUM(
                    'subir',
                    'descargar',
                    'eliminar',
                    'editar',
                    'crear_carpeta',
                    'mover',
                    'ver',
                    'solicitar_acceso',
                    'aprobar_solicitud',
                    'rechazar_solicitud',
                    'solicitar_subida',
                    'aprobar_subida',
                    'rechazar_subida',
                    'restaurar_version',
                    'iniciar_sesion',
                    'cerrar_sesion',
                    'login_fallido',
                    'usuario_bloqueado'
                ) NOT NULL
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("
                ALTER TABLE registro_de_actividad
                MODIFY COLUMN accion ENUM(
                    'subir',
                    'descargar',
                    'eliminar',
                    'editar',
                    'crear_carpeta',
                    'mover',
                    'ver',
                    'solicitar_acceso',
                    'aprobar_solicitud',
                    'rechazar_solicitud',
                    'restaurar_version',
                    'iniciar_sesion',
                    'cerrar_sesion',
                    'login_fallido',
                    'usuario_bloqueado'
                ) NOT NULL
            ");
        }
    }
};