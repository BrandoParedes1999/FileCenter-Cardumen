<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_de_carpeta', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('carpeta_id');
            $table->unsignedInteger('usuario_id')->nullable(); // NULL = permiso por rol (no por usuario específico)
            $table->unsignedInteger('empresa_id')->nullable(); // Para permisos empresa + rol
            $table->enum('rol', [
                'Admin',
                'Gerente',
                'Auxiliar',
                'Empleado',
            ])->nullable()->default(null);

            // Bits de permiso
            $table->boolean('puede_leer')->default(false);
            $table->boolean('puede_subir')->default(false);
            $table->boolean('puede_editar')->default(false);
            $table->boolean('puede_borrar')->default(false);
            $table->boolean('puede_descargar')->default(false);
            $table->boolean('heredar')->default(true); // true = se hereda a subcarpetas

            $table->unsignedInteger('concedido_por');

            $table->timestamps();

            // UNIQUE: imposible crear permiso duplicado para el mismo usuario en la misma carpeta
            $table->unique(['carpeta_id', 'usuario_id'], 'uq_perm_carpeta_usuario');

            // UNIQUE: imposible duplicar permiso de rol dentro de una empresa en la misma carpeta
            $table->unique(['carpeta_id', 'empresa_id', 'rol'], 'uq_perm_carpeta_rol');

            // Índices de búsqueda frecuente
            $table->index('usuario_id',              'idx_perm_usuario');
            $table->index('empresa_id',              'idx_perm_empresa');
            $table->index('concedido_por',           'idx_perm_concedido');
            $table->index(['carpeta_id', 'usuario_id'], 'idx_perm_carpeta_usr');

            // Foreign keys
            $table->foreign('carpeta_id', 'fk_perm_carpeta')
                ->references('id')->on('carpetas')
                ->onDelete('cascade')->onUpdate('no action');

            $table->foreign('usuario_id', 'fk_perm_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('empresa_id', 'fk_perm_empresa')
                ->references('id')->on('empresas')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('concedido_por', 'fk_perm_concedido')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_de_carpeta');
    }
};
