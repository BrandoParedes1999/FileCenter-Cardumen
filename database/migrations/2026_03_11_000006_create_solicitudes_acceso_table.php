<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_acceso', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('solicitante_id');
            $table->unsignedInteger('empresa_objetivo_id');
            $table->unsignedInteger('carpeta_id')->nullable();
            $table->unsignedInteger('archivo_id')->nullable();
            $table->text('razon');                           // justificación del solicitante
            $table->enum('status', [
                'Pendiente',
                'Aprobado',
                'Rechazado',
            ])->default('Pendiente');
            $table->unsignedInteger('revisado_por')->nullable();
            $table->timestamp('revisado_en')->nullable();
            $table->text('comentario_revisor')->nullable();  // respuesta del Admin al solicitante
            $table->enum('tipo_acceso', [
                'Lectura',
                'Descargar',
            ])->default('Lectura');
            $table->timestamp('caduca_en')->nullable();      // NULL = acceso permanente

            $table->timestamps();

            // Índices
            $table->index('solicitante_id',                          'idx_sol_solicitante');
            $table->index('carpeta_id',                              'idx_sol_carpeta');
            $table->index('archivo_id',                              'idx_sol_archivo');
            $table->index('revisado_por',                            'idx_sol_revisor');
            $table->index(['empresa_objetivo_id', 'status'],         'idx_sol_empresa_status');

            // Foreign keys
            $table->foreign('solicitante_id', 'fk_sol_solicitante')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('empresa_objetivo_id', 'fk_sol_empresa')
                ->references('id')->on('empresas')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('carpeta_id', 'fk_sol_carpeta')
                ->references('id')->on('carpetas')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('archivo_id', 'fk_sol_archivo')
                ->references('id')->on('archivos')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('revisado_por', 'fk_sol_revisor')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_acceso');
    }
};
