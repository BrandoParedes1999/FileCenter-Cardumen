<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('carpeta_id');
            $table->unsignedInteger('subido_por');
            $table->string('nombre_original', 245);         // "Contrato Enero 2025.pdf"
            $table->string('nombre_almacenamiento', 245);   // "f3a8c2d1-uuid.pdf" en disco
            $table->string('ruta_disco', 500);              // "empresa-1/ventas/2024/"
            $table->string('hash_sha256', 64)->nullable();  // integridad + detección duplicados
            $table->string('tipo_mime', 100)->nullable();
            $table->string('extension', 20);
            $table->unsignedBigInteger('tamanio_bytes')->default(0);
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('version')->default(1); // versión actual
            $table->unsignedInteger('numero_descargas')->default(0);
            $table->boolean('esta_eliminado')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('carpeta_id',                      'idx_archivos_carpeta');
            $table->index('subido_por',                      'idx_archivos_subido_por');
            $table->index(['carpeta_id', 'esta_eliminado'],  'idx_archivos_carpeta_activos');
            $table->index('hash_sha256',                     'idx_archivos_hash');

            // Foreign keys
            $table->foreign('carpeta_id', 'fk_archivos_carpeta')
                ->references('id')->on('carpetas')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('subido_por', 'fk_archivos_subidor')
                ->references('id')->on('usuarios')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
