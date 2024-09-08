<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Models\Cliente;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'apellidos' => ['required', 'string', 'max:255'],
            'rolID' => ['required', 'integer', 'exists:roles,rolID'],
            'direccion' => ['required', 'string', 'max:255'], 
            'telefono' => ['required', 'regex:/^6[0-9]{8}$/'],
            'dni' => ['required', 'regex:/^[0-9]{8}[A-Z]{1}$/'], 
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Crear el cliente asociado
        Cliente::create([
            'nombre' => $input['name'],
            'apellidos' => $input['apellidos'],
            'direccion' => $input['direccion'],
            'telefono' => $input['telefono'],
            'dni' => $input['dni'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'apellidos' => $input['apellidos'],
            'rolID' => $input['rolID'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
