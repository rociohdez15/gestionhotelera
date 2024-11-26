<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Habitacion;
use App\Models\User;
use App\Models\Cliente;
use App\Models\EdadNino;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use TCPDF;

class ListarUsuariosControlador extends Controller
{
    public function listarUsuarios(Request $request)
    {
        $query = DB::table('users')
            ->select(
                'id',
                DB::raw("CONCAT(name, ', ', apellidos) as nombre_completo"),
                'email',
                'rolID'
            )
            ->where('rolID', 2); // Filtrar por rolID igual a 2

        $totalUsuarios = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalUsuarios / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $usuarios = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'usuarios' => $usuarios,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarusuarios', $parametros);
    }
    public function delUsuario($id, Request $request)
    {
        $usuario = DB::table('users')->where('id', $id)->first();

        if (!$usuario) {
            return back()->withError('El usuario especificado no existe.');
        }

        DB::table('users')->where('id', $id)->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'El usuario se ha eliminado correctamente.',
                'usuario' => $usuario
            ]);
        }

        return redirect()->route('listarUsuarios')->with('status', 'El usuario se ha eliminado correctamente.');
    }

    public function actualizarUsuarios()
    {
        $usuarios = DB::table('users')
            ->select('id', 'name', 'apellidos', 'email', 'rolID');

        return response()->json($usuarios);
    }

    public function mostrarUsuario(Request $request, $usuarioID)
    {
        $usuario = DB::table('users')
            ->select(
                'id',
                'name',
                'apellidos',
                'email',
                'rolID' // Mostrar el rolID tal y como es en la base de datos
            )
            ->where('id', $usuarioID)
            ->first();

        if (!$usuario) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }
            return back()->withError('Usuario no encontrado');
        }

        $parametros = compact('usuario');

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('editarusuario', $parametros);
    }

    public function generarPDF($usuarioID)
    {
        $usuario = DB::table('users')
            ->select(
                'id',
                'name',
                'apellidos',
                'email',
                'rolID'
            )
            ->where('id', $usuarioID)
            ->first();

        $pdf = new TCPDF();

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos del Usuario', 0, 1, 'C');

        $html = '<div style="font-size: 12px; text-align: center;">';

        if ($usuario) {
            $html .= '<div style="margin-bottom: 20px;">';
            $html .= '<strong>ID Usuario:</strong> ' . $usuario->id . '<br>';
            $html .= '<strong>Nombre:</strong> ' . $usuario->name . ' ' . $usuario->apellidos . '<br>';
            $html .= '<strong>Email:</strong> ' . $usuario->email . '<br>';
            $html .= '<strong>Rol ID:</strong> ' . $usuario->rolID . '<br>';
            $html .= '</div>';
        } else {
            $html .= '<div>No se encontraron datos para el usuario especificado.</div>';
        }

        $html .= '</div>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "usuario_" . $usuarioID . ".pdf";

        $pdf->Output($filename, 'D');
    }

    public function editarUsuario(Request $request, $id)
    {
        $usuario = User::find($id);
        if (!$usuario) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            return back()->withError('Usuario no encontrado');
        }

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            // Comprobar si el email ha cambiado y si ya existe
            if ($usuario->email !== $validatedData['email']) {
                $emailExists = User::where('email', $validatedData['email'])->exists();
                if ($emailExists) {
                    if ($request->wantsJson() || $request->is('api/*')) {
                        return response()->json(['message' => 'El correo electrónico ya está registrado.'], 422);
                    }
                    return back()->withErrors(['email' => 'El correo electrónico ya está en uso.'])->withInput();
                }
            }

            $usuario->name = $validatedData['name'];
            $usuario->apellidos = $validatedData['apellidos'];
            $usuario->email = $validatedData['email'];
            $usuario->rolID = 2; // Asignar siempre el rol ID 2
            $usuario->save();

            $usuario->refresh();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'El usuario se ha editado correctamente.',
                    'usuario' => $usuario
                ]);
            }

            return response()->json(['success' => 'El usuario se ha editado correctamente']);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al editar el usuario', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al editar el usuario')->withInput();
        }
    }
    public function generarPDFTotal()
    {
        $usuarios = DB::table('users')
            ->select(
                'id',
                'name',
                'apellidos',
                'email',
                'rolID'
            )
            ->where('rolID', 2) // Filtrar por rolID igual a 2
            ->get();

        $pdf = new TCPDF();

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Listado de Usuarios', 0, 1, 'C');

        $html = '<table border="1" style="font-size: 10px;">';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th>ID Usuario</th>';
        $html .= '<th>Nombre Completo</th>';
        $html .= '<th>Email</th>';
        $html .= '<th>Rol ID</th>';
        $html .= '</tr>';

        foreach ($usuarios as $usuario) {
            $html .= '<tr>';
            $html .= '<td>' . $usuario->id . '</td>';
            $html .= '<td>' . $usuario->name . ' ' . $usuario->apellidos . '</td>';
            $html .= '<td>' . $usuario->email . '</td>';
            $html .= '<td>' . $usuario->rolID . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "lista_usuarios.pdf";

        $pdf->Output($filename, 'D');
    }

    public function buscarUsuarios(Request $request)
    {
        $query = $request->input('query');

        $usuarios = DB::table('users')
            ->select(
                'id',
                DB::raw("CONCAT(name, ', ', apellidos) as nombre_completo"),
                'email',
                'rolID'
            )
            ->where('rolID', 2) // Filtrar por rolID igual a 2
            ->where(function ($q) use ($query) {
                $q->where('id', 'LIKE', "%$query%")
                    ->orWhere('name', 'LIKE', "%$query%")
                    ->orWhere('apellidos', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%");
            });

        $usuarios = $usuarios->orderBy('id', 'asc')->paginate(5);

        if ($request->ajax()) {
            return response()->json($usuarios);
        }

        return view('listarusuarios', compact('usuarios'));
    }

    public function mostrarUsuarios(Request $request)
    {
        $usuarios = User::all();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['usuarios' => $usuarios]);
        }

        return view('anadirusuario', ['usuarios' => $usuarios]);
    }


    public function anadirUsuario(Request $request)
    {
        // Registrar la URL y los datos de la solicitud
        Log::info('URL de la solicitud: ' . $request->fullUrl());
        Log::info('Datos de la solicitud: ', $request->all());

        try {
            // Validar los campos excepto el email (lo validamos manualmente)
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:6',
                'rolID' => 'required|integer|exists:roles,rolID',
            ]);

            Log::info('Datos validados: ', $validatedData);

            // Comprobar si el correo ya existe
            $emailExistente = User::where('email', $validatedData['email'])->first();
            if ($emailExistente) {
                Log::warning('Correo duplicado: ' . $validatedData['email']);
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'El correo electrónico ya está registrado.',
                        'errors' => ['email' => ['El correo electrónico ya está en uso.']]
                    ], 422);
                }
                return back()->withErrors(['email' => 'El correo electrónico ya está en uso.'])->withInput();
            }

            // Crear el usuario
            $usuario = new User();
            $usuario->name = $validatedData['name'];
            $usuario->apellidos = $validatedData['apellidos'];
            $usuario->email = $validatedData['email'];
            $usuario->password = bcrypt($validatedData['password']); // Encriptar la contraseña
            $usuario->rolID = 2; // Asignar el rol ID del formulario
            $usuario->save();

            Log::info('Usuario creado: ', $usuario->toArray());

            // Responder según el tipo de solicitud
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Usuario añadido correctamente.',
                    'usuario' => $usuario
                ]);
            }

            return response()->json([
                'message' => 'Usuario añadido correctamente.',
                'usuario' => $usuario
            ]);
        } catch (ValidationException $e) {
            Log::error('Error de validación al añadir el usuario: ' . $e->getMessage());
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al añadir el usuario: ' . $e->getMessage());
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al añadir el usuario', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al añadir el usuario')->withInput();
        }
    }

    public function comprobarEmail(Request $request)
    {
        $email = $request->query('email');
        $exists = User::where('email', $email)->exists();

        return response()->json(['exists' => $exists]);
    }
}
