<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Habitacion;
use App\Models\User;
use App\Models\EdadNino;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use TCPDF;

class ListarHabitacionesControlador extends Controller
{
    public function listarHabitaciones(Request $request)
    {
        $query = DB::table('habitaciones')
            ->select('habitacionID', 'numhabitacion', 'tipohabitacion', 'precio', 'hotelID');

        $totalHabitaciones = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHabitaciones / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $habitaciones = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'habitaciones' => $habitaciones,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarhabitaciones', $parametros);
    }

    public function delHabitacion($habitacionID, Request $request)
    {
        $habitacion = DB::table('habitaciones')->where('habitacionID', $habitacionID)->first();

        if (!$habitacion) {
            return back()->withError('La habitación especificada no existe.');
        }

        DB::table('habitaciones')->where('habitacionID', $habitacionID)->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'La habitación se ha eliminado correctamente.',
                'habitacion' => $habitacion
            ]);
        }

        return redirect()->route('listarHabitaciones')->with('status', 'La habitación se ha eliminado correctamente.');
    }

    public function actualizarHabitaciones()
    {
        $habitaciones = DB::table('habitaciones')
            ->select('habitacionID', 'numhabitacion', 'tipohabitacion', 'precio', 'hotelID');


        return response()->json($habitaciones);
    }

    public function mostrarHabitacion(Request $request, $habitacionID)
    {
        $habitacion = Habitacion::find($habitacionID);

        if (!$habitacion) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Habitación no encontrada'], 404);
            }
            return back()->withError('Habitación no encontrada');
        }

        $parametros = compact('habitacion');

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('editarhabitacion', $parametros);
    }

    public function generarPDF($habitacionID)
    {
        $habitacion = DB::table('habitaciones')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->select(
                'habitaciones.*',
                'hoteles.nombre as nombre_hotel'
            )
            ->where('habitaciones.habitacionID', $habitacionID)
            ->first();

        $pdf = new TCPDF();

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos de la Habitación', 0, 1, 'C');

        $html = '<div style="font-size: 12px; text-align: center;">';

        if ($habitacion) {
            $html .= '<div style="margin-bottom: 20px;">';
            $html .= '<strong>ID Habitación:</strong> ' . $habitacion->habitacionID . '<br>';
            $html .= '<strong>Número de Habitación:</strong> ' . $habitacion->numhabitacion . '<br>';
            $html .= '<strong>Tipo de Habitación:</strong> ' . $habitacion->tipohabitacion . '<br>';
            $html .= '<strong>Precio:</strong> ' . $habitacion->precio . '<br>';
            $html .= '<strong>Habitacion:</strong> ' . $habitacion->nombre_hotel . '<br>';
            $html .= '</div>';
        } else {
            $html .= '<div>No se encontraron datos para la habitación especificada.</div>';
        }

        $html .= '</div>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "habitacion_" . $habitacionID . ".pdf";

        $pdf->Output($filename, 'D');
    }

    public function editarHabitacion(Request $request, $habitacionID)
    {
        $habitacion = Habitacion::find($habitacionID);
        if (!$habitacion) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Habitación no encontrada'], 404);
            }
            return back()->withError('Habitación no encontrada');
        }

        try {
            $validatedData = $request->validate([
                'numhabitacion' => 'required|regex:/^[0-9]+$/', 
                'tipohabitacion' => 'required|integer|min:1', 
                'precio' => 'required|regex:/^\d+(\.\d{1,2})?$/', 
                'hotelID' => 'required|integer|exists:hoteles,hotelID', 
            ]);
    


            $habitacion->numhabitacion = $validatedData['numhabitacion'];
            $habitacion->tipohabitacion = $validatedData['tipohabitacion'];
            $habitacion->precio = $validatedData['precio'];
            $habitacion->hotelID = $validatedData['hotelID'];
            $habitacion->save();

            $habitacion->refresh();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'La habitación se ha editado correctamente.',
                    'habitacion' => $habitacion
                ]);
            }

            return response()->json(['success' => 'La habitación se ha editado correctamente']);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al editar la habitación', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al editar la habitación')->withInput();
        }
    }

    public function generarPDFTotal()
    {
        $habitaciones = DB::table('habitaciones')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->select(
                'habitaciones.*',
                'hoteles.nombre as nombre_hotel'
            )
            ->get();

        $pdf = new TCPDF();

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Listado de Habitaciones', 0, 1, 'C');

        $html = '<table border="1" style="font-size: 10px;">';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th>ID Habitación</th>';
        $html .= '<th>Número de Habitación</th>';
        $html .= '<th>Tipo de Habitación</th>';
        $html .= '<th>Precio</th>';
        $html .= '<th>Habitacion</th>';
        $html .= '</tr>';

        foreach ($habitaciones as $habitacion) {
            $html .= '<tr>';
            $html .= '<td>' . $habitacion->habitacionID . '</td>'; 
            $html .= '<td>' . $habitacion->numhabitacion . '</td>'; 
            $html .= '<td>' . $habitacion->tipohabitacion . '</td>'; 
            $html .= '<td>' . $habitacion->precio . '</td>'; 
            $html .= '<td>' . $habitacion->nombre_hotel . '</td>'; 
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "lista_habitaciones.pdf";

        $pdf->Output($filename, 'D');
    }

    public function buscarHabitaciones(Request $request)
    {
        $query = $request->input('query');

       
        $habitaciones = DB::table('habitaciones')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->select('habitaciones.*', 'hoteles.nombre as nombre_hotel')
            ->where(function ($q) use ($query) {
                $q->where('habitaciones.habitacionID', 'LIKE', "%$query%")
                    ->orWhere('habitaciones.numhabitacion', 'LIKE', "%$query%")
                    ->orWhere('habitaciones.tipohabitacion', 'LIKE', "%$query%")
                    ->orWhere('habitaciones.precio', 'LIKE', "%$query%")
                    ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
                    ->orWhere('habitaciones.hotelID', 'LIKE', "%$query%");
            });

        $habitaciones = $habitaciones->orderBy('habitacionID', 'asc')->paginate(5);


        if ($request->ajax()) {
            return response()->json($habitaciones);
        }

        return view('listarhabitaciones', compact('habitaciones'));
    }

    public function mostrarHabitaciones(Request $request)
    {
        $habitaciones = Habitacion::all();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['habitaciones' => $habitaciones]);
        }

        return view('anadirhabitacion', ['habitaciones' => $habitaciones]);
    }


public function anadirUsuario(Request $request)
{
    // Registrar la URL y los datos de la solicitud
    Log::info('URL de la solicitud: ' . $request->fullUrl());
    Log::info('Datos de la solicitud: ', $request->all());

    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'rolID' => 'required|integer|exists:roles,rolID',
        ]);

        Log::info('Datos validados: ', $validatedData);

        $usuario = new User();
        $usuario->name = $validatedData['name'];
        $usuario->apellidos = $validatedData['apellidos'];
        $usuario->email = $validatedData['email'];
        $usuario->password = bcrypt($validatedData['password']); // Encriptar la contraseña
        $usuario->rolID = 2; // Asignar un rol por defecto
        $usuario->save();

        Log::info('Usuario creado: ', $usuario->toArray());

        $usuario->refresh();

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
    } catch (\Exception $e) {
        Log::error('Error al añadir el usuario: ' . $e->getMessage());
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Error al añadir el usuario', 'error' => $e->getMessage()], 500);
        }
        return back()->withError('Error al añadir el usuario')->withInput();
    }
}
}
