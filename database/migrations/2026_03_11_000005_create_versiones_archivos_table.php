<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versiones_archivos', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('archivo_id');
            $table->unsignedSmallInteger('version');         // número de esta versión (1, 2, 3...)
            $table->string('nombre_original', 245);          // nombre al momento de subir
            $table->string('nombre_almacenamiento', 245);    // UUID.ext — archivo en disco (no se borra)
            $table->string('ruta_disco', 500);
            $table->string('hash_sha256', 64)->nullable();   // integridad de esta versión
            $table->unsignedBigInteger('tamanio_bytes')->default(0);
            $table->unsignedInteger('subido_por');
            $table->string('nota_version', 500)->nullable(); // "Contrato revisado por legal"
            $table->boolean('activo')->default(true);        // false = marcada para purga

            // Sin updated_at — los registros de versión son INMUTABLES
            $table->timestamp('created_at')->nullable()->useCurrent();

            // Índices
            $table->index('archivo_id',              'idx_ver_archivo');
            $table->index(['archivo_id', 'version'], 'idx_ver_archivo_num');

            // UNIQUE: no puede haber dos filas con el mismo número de versión para el mismo archivo
            $table->unique(['archivo_id', 'version'], 'uq_ver_archivo_num');

            // Foreign keys
            $table->foreign('archivo_id', 'fk_ver_archivo')
                ->references('id')->on('archivos')
                ->onDelete('cascade')->onUpdate('no action');

            $table->foreign('subido_por', 'fk_ver_subidor')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versiones_archivos');
    }
};
