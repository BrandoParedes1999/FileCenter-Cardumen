<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    protected $model = \App\Models\Usuario::class;

    public function definition(): array
    {
        return [
            'empresa_id'      => Empresa::factory(),
            'nombre'          => fake()->firstName(),
            'paterno'         => fake()->lastName(),
            'materno'         => fake()->lastName(),
            'email'           => fake()->unique()->safeEmail(),
            'password'        => Hash::make('password'),
            'rol'             => 'Empleado',
            'departamento'    => fake()->word(),
            'avatar'          => null,
            'es_activo'       => true,
            'intentos_login'  => 0,
            'bloqueado_hasta' => null,
            'last_login'      => null,
        ];
    }

    public function superadmin(): static
    {
        return $this->state(['rol' => 'Superadmin']);
    }

    public function auxQhse(): static
    {
        return $this->state(['rol' => 'Aux_QHSE']);
    }

    public function admin(): static
    {
        return $this->state(['rol' => 'Admin']);
    }

    public function gerente(): static
    {
        return $this->state(['rol' => 'Gerente']);
    }

    public function auxiliar(): static
    {
        return $this->state(['rol' => 'Auxiliar']);
    }

    public function empleado(): static
    {
        return $this->state(['rol' => 'Empleado']);
    }

    public function inactivo(): static
    {
        return $this->state(['es_activo' => false]);
    }

    public function bloqueado(): static
    {
        return $this->state([
            'bloqueado_hasta' => now()->addMinutes(15),
            'intentos_login'  => 0,
        ]);
    }

    public function deEmpresa(Empresa $empresa): static
    {
        return $this->state(['empresa_id' => $empresa->id]);
    }
}
