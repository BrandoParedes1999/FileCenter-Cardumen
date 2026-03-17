<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_de_actividad', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('usuario_id');
            $table->enum('accion', [
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
                'restaurar_version',    // recuperar versión anterior de un archivo
                'iniciar_sesion',
                'cerrar_sesion',
                'login_fallido',        // auditoría de seguridad
                'usuario_bloqueado',    // auditoría de seguridad
            ]);
            $table->enum('recurso', [
                'archivo',
                'carpeta',
                'solicitud',
                'usuario',
                'version',
            ]);
            $table->unsignedInteger('recurso_id');
            $table->text('detalles')->nullable();
            $table->string('ip_address', 45)->nullable(); // IPv4 o IPv6

            // Sin updated_at — los logs son INMUTABLES por diseño
            $table->timestamp('created_at')->nullable()->useCurrent();

            // Índices
            $table->index('created_at',                  'idx_log_created');
            $table->index(['usuario_id', 'created_at'],  'idx_log_usuario_fecha');

            // Foreign key
            $table->foreign('usuario_id', 'fk_log_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_de_actividad');
    }
};
