<?php

namespace Database\Factories;

use App\Models\Carpeta;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchivoFactory extends Factory
{
    protected $model = \App\Models\Archivo::class;

    public function definition(): array
    {
        $extension = fake()->randomElement(['pdf', 'docx', 'xlsx']);

        return [
            'carpeta_id'            => Carpeta::factory(),
            'subido_por'            => Usuario::factory(),
            'nombre_original'       => fake()->word() . '.' . $extension,
            'nombre_almacenamiento' => fake()->uuid() . '.' . $extension,
            'ruta_disco'            => 'empresa_1/carpeta_1/' . fake()->uuid() . '.' . $extension,
            'hash_sha256'           => fake()->sha256(),
            'tipo_mime'             => $this->mimeParaExtension($extension),
            'extension'             => $extension,
            'tamanio_bytes'         => fake()->numberBetween(1024, 5242880),
            'descripcion'           => null,
            'version'               => 1,
            'numero_descargas'      => 0,
            'esta_eliminado'        => false,
        ];
    }

    public function pdf(): static
    {
        return $this->state([
            'extension' => 'pdf',
            'tipo_mime' => 'application/pdf',
        ]);
    }

    public function enCarpeta(Carpeta $carpeta): static
    {
        return $this->state(['carpeta_id' => $carpeta->id]);
    }

    public function subidoPor(Usuario $usuario): static
    {
        return $this->state(['subido_por' => $usuario->id]);
    }

    private function mimeParaExtension(string $ext): string
    {
        return match ($ext) {
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };
    }
}
