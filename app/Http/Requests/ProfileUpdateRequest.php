<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:245'],
            'paterno'     => ['required', 'string', 'max:245'],
            'materno'     => ['nullable', 'string', 'max:245'],
            'departamento'=> ['nullable', 'string', 'max:245'],
            'email'       => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:245',
                Rule::unique(Usuario::class)->ignore($this->user()->id),
            ],
        ];
    }
}