<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('nombre', 245);
            $table->string('siglas', 80)->unique();
            $table->string('logo', 245);
            $table->tinyInteger('es_corporativo')->default(0); // 1 = Corporativo raíz
            $table->string('color_primario', 10)->nullable();  
            $table->string('color_secundario', 10)->nullable();
            $table->string('color_terciario', 10)->nullable();
            $table->tinyInteger('activo')->default(1);
            $table->timestamps(); // created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
