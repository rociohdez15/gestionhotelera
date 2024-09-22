<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Resena;
use Carbon\Carbon;


class EditarPerfilControlador extends Controller
{
    //Método que mostrará el formulario para actualizar el perfil del usuario
    public function mostrarPerfil(Request $request, $clienteID)
    {
        // Obtener el usuario que se va a editar
        $cliente = Cliente::find($clienteID);
        $usuario = Auth::user();

        return view('editarperfil', compact('cliente','usuario'),  ['cliente' => $cliente], ['usuario' => $usuario]);
    }

    //Método que actualiza el perfil del usuario (cambia los datos antiguos por los nuevos introducidos)
    public function editarPerfil(Request $request, $clienteID, $id)
    {
        // Validaciones de los campos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => ['required', 'regex:/^[67]\d{8}$/'],
            'dni' => ['required', 'regex:/^\d{8}[A-Za-z]$/'],
            'email' => 'required|string|email|max:255|unique:clientes,email,' . $clienteID . ',clienteID'

        ]);

        // Encuentra el cliente y el usuario por ID
        $cliente = Cliente::find($clienteID);
        $usuario = User::find($id);

        // Verifica si el cliente existe
        if (!$cliente) {
            return redirect()->route('editarperfil')->withErrors('El cliente no existe.');
        }

        // Actualiza los datos del cliente
        $cliente->nombre = $request->nombre;
        $cliente->apellidos = $request->apellidos;
        $cliente->direccion = $request->direccion;
        $cliente->telefono = $request->telefono;
        $cliente->dni = $request->dni;
        $cliente->email = $request->email;

        //Actualiza los datos del usuario
        $usuario->name = $request->nombre;
        $usuario->apellidos = $request->apellidos;
        $usuario->email = $request->email;

        // Guarda los cambios en ambas tablas
        $cliente->save();
        $usuario->save();

        // Redirige con mensaje de éxito
        return redirect()->route('editarperfil', ['clienteID' => $clienteID,'id' => $usuario->id ])->with('status', 'El perfil ha sido actualizado correctamente.');
    }
}
