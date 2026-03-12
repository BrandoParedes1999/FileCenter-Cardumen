<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('empresa_id');
            $table->string('nombre', 245);
            $table->string('paterno', 245);
            $table->string('materno', 245);
            $table->string('email', 245)->unique();
            $table->string('password', 255);
            $table->enum('rol', [
                'Superadmin',
                'Aux_QHSE',
                'Admin',
                'Gerente',
                'Auxiliar',
                'Empleado',
            ])->default('Empleado');
            $table->string('departamento', 245)->nullable();
            $table->string('avatar', 245)->nullable();
            $table->tinyInteger('es_activo')->default(1);

            // Seguridad
            $table->unsignedTinyInteger('intentos_login')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable(); // NULL = libre

            // Laravel Auth
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();                        
            $table->timestamp('last_login')->nullable();

            $table->timestamps();   // created_at + updated_at
            $table->softDeletes();  // deleted_at

            // Índices
            $table->index('empresa_id', 'idx_usuarios_empresa');
            $table->index('bloqueado_hasta', 'idx_usuarios_bloqueado');

            // Foreign key
            $table->foreign('empresa_id', 'fk_usuarios_empresa')
                ->references('id')->on('empresas')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
