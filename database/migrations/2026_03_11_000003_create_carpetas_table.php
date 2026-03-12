<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carpetas', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('padre_id')->nullable(); // NULL = carpeta raíz
            $table->string('nombre', 245);
            $table->string('path', 500);                    // /empresa-1/ventas/2024
            $table->tinyInteger('es_publico')->default(0);  // 1 = visible para todos
            $table->unsignedInteger('creado_por');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('empresa_id', 'idx_carpetas_empresa');
            $table->index('padre_id',   'idx_carpetas_padre');
            $table->index('creado_por', 'idx_carpetas_creador');

            // Foreign keys
            $table->foreign('empresa_id', 'fk_carpetas_empresa')
                ->references('id')->on('empresas')
                ->onDelete('no action')->onUpdate('no action');

            // Auto-referencia: carpeta → carpeta padre
            $table->foreign('padre_id', 'fk_carpetas_padre')
                ->references('id')->on('carpetas')
                ->onDelete('cascade')->onUpdate('no action');

            $table->foreign('creado_por', 'fk_carpetas_creador')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carpetas');
    }
};
