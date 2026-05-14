<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
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
                    'crear_usuario',
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
};
