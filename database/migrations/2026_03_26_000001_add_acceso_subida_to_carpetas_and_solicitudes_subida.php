<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carpetas', function (Blueprint $table) {

            $table->enum('modo_acceso', ['solo_lectura', 'con_descarga', 'normal'])
                ->default('normal')
                ->after('es_publico')
                ->comment('Control de acceso base de la carpeta');


            $table->boolean('requiere_aprobacion_subida')
                ->default(false)
                ->after('modo_acceso')
                ->comment('Si true, subidas de roles menores quedan pendientes');
        });

        // ── 2. Tabla de solicitudes de subida ────────────────────
        Schema::create('solicitudes_subida', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('carpeta_id');
            $table->unsignedInteger('solicitante_id');

            // Datos del archivo temporal
            $table->string('nombre_original', 245);
            $table->string('nombre_almacenamiento', 245);
            $table->string('ruta_temporal', 500);
            $table->string('hash_sha256', 64)->nullable();
            $table->string('tipo_mime', 100)->nullable();
            $table->string('extension', 10);
            $table->unsignedBigInteger('tamanio_bytes')->default(0);
            $table->text('descripcion')->nullable();

            // Control de flujo
            $table->enum('status', ['Pendiente', 'Aprobado', 'Rechazado'])->default('Pendiente');
            $table->unsignedInteger('revisado_por')->nullable();
            $table->timestamp('revisado_en')->nullable();
            $table->text('comentario_revisor')->nullable();

            // Referencia al archivo creado si fue aprobado
            $table->unsignedInteger('archivo_id')->nullable();

            $table->timestamps();

            // Índices
            $table->index('carpeta_id',     'idx_sol_subida_carpeta');
            $table->index('solicitante_id', 'idx_sol_subida_solicitante');
            $table->index('status',         'idx_sol_subida_status');

            // Foreign keys
            $table->foreign('carpeta_id', 'fk_sol_subida_carpeta')
                ->references('id')->on('carpetas')
                ->onDelete('no action');

            $table->foreign('solicitante_id', 'fk_sol_subida_solicitante')
                ->references('id')->on('usuarios')
                ->onDelete('no action');

            $table->foreign('revisado_por', 'fk_sol_subida_revisor')
                ->references('id')->on('usuarios')
                ->onDelete('no action');

            $table->foreign('archivo_id', 'fk_sol_subida_archivo')
                ->references('id')->on('archivos')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_subida');

        Schema::table('carpetas', function (Blueprint $table) {
            $table->dropColumn(['modo_acceso', 'requiere_aprobacion_subida']);
        });
    }
};