<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;


class OlvidoPasswordControlador extends Controller{
    public function olvidoPassword(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');

        // Comprueba si el email de ese usuario existe
        $user = DB::table('users')->where('email', $request->email)->first();

        // Si el email del usuario no existe devuelve un mensaje de error
        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'No se encontró un usuario con esa dirección de correo electrónico.'])->withInput();
        }

        // IActualiza la contraseña en la table 'users'
        DB::table('users')
                    ->where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);
        
        // Actualizar la contraseña en la tabla 'clientes'
        DB::table('clientes')
            ->where('email', $email)
            ->update(['password' => Hash::make($password)]);

        return redirect()->route('olvidoPassword')->with('status', '¡Contraseña actualizada exitosamente!');
        
    }
}