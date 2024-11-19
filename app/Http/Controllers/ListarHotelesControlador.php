<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\EdadNino;
use App\Models\Reserva;
use App\Models\Habitacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use TCPDF;

class ListarHotelesControlador extends Controller
{
    public function listarHoteles(Request $request)
    {
        $query = DB::table('hoteles')
            ->select('hotelID', 'nombre', 'direccion', 'ciudad', 'telefono', 'descripcion');

        $totalHoteles = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHoteles / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $hoteles = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'hoteles' => $hoteles,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarhoteles', $parametros);
    }
    public function delHotel($hotelID, Request $request)
    {
        // Buscar el hotel
        $hotel = Hotel::find($hotelID);

        if (!$hotel) {
            return back()->withError('El hotel especificado no existe.');
        }

        // Eliminar las habitaciones asociadas al hotel
        DB::table('habitaciones')->where('hotelID', $hotelID)->delete();

        // Eliminar el hotel
        $hotel->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'El hotel se ha eliminado correctamente.',
                'hotel' => $hotel
            ]);
        }

        return redirect()->route('listarHoteles')->with('status', 'El hotel se ha eliminado correctamente.');
    }

    public function mostrarHotel(Request $request, $hotelID)
    {
        $hotel = Hotel::find($hotelID);

        if (!$hotel) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Hotel no encontrado'], 404);
            }
            return back()->withError('Hotel no encontrado');
        }

        $parametros = compact('hotel');

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('editarhotel', $parametros);
    }
    
public function editarHotel(Request $request, $hotelID)
{
    $hotel = Hotel::find($hotelID);
    if (!$hotel) {
        Log::error('Hotel no encontrado', ['hotelID' => $hotelID]);
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Hotel no encontrado'], 404);
        }
        return back()->withError('Hotel no encontrado');
    }

    try {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'descripcion' => 'nullable|string',
        ]);

        // Actualizar los datos del hotel
        $hotel->nombre = $validatedData['nombre'];
        $hotel->direccion = $validatedData['direccion'];
        $hotel->ciudad = $validatedData['ciudad'];
        $hotel->telefono = $validatedData['telefono'];
        $hotel->descripcion = $validatedData['descripcion'];
        $hotel->save();

        // Recargar el hotel para obtener los datos actualizados
        $hotel->refresh();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'El hotel se ha editado correctamente.',
                'hotel' => $hotel
            ]);
        }

        // Obtener la lista de hoteles para pasarla a la vista
        $hoteles = Hotel::all();
        $pagina_actual = 'gestionarhoteles'; // Define la variable $pagina_actual
        $registros_por_pagina = 10; // Define la variable $registros_por_pagina
        $total_paginas = ceil($hoteles->count() / $registros_por_pagina); // Define la variable $total_paginas

        // Redirigir a la vista con el mensaje de estado, la lista de hoteles, la página actual y el total de páginas
        return view('listarhoteles')->with([
            'status' => 'El hotel se ha editado correctamente.',
            'hoteles' => $hoteles,
            'pagina_actual' => $pagina_actual,
            'total_paginas' => $total_paginas,
            'registros_por_pagina' => $registros_por_pagina
        ]);
    } catch (\Exception $e) {
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Error al editar el hotel', 'error' => $e->getMessage()], 500);
        }
        return back()->withError('Error al editar el hotel')->withInput();
    }
}




    public function comprobarReserva(Request $request, $hotelID)
    {
        try {
            $request->validate([
                'fechaEntrada' => 'required|date',
                'fechaSalida' => 'required|date|after_or_equal:fechaEntrada',
            ]);

            $fechaEntrada = $request->query('fechaEntrada');
            $fechaSalida = $request->query('fechaSalida');

            $existeReserva = DB::table('reservas')
                ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
                ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
                ->where('hoteles.hotelID', $hotelID)
                ->where(function ($query) use ($fechaEntrada, $fechaSalida) {
                    $query->where(function ($subQuery) use ($fechaEntrada, $fechaSalida) {
                        $subQuery->where('reservas.fechainicio', '<=', $fechaSalida)
                            ->where('reservas.fechafin', '>=', $fechaEntrada);
                    })
                        ->orWhere(function ($subQuery) use ($fechaEntrada, $fechaSalida) {
                            $subQuery->where('reservas.fechainicio', '>=', $fechaEntrada)
                                ->where('reservas.fechafin', '<=', $fechaSalida);
                        });
                })
                ->exists();

            return response()->json($existeReserva);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en el servidor al comprobar la reserva'], 500);
        }
    }
    public function verificarHabitacion($hotelID, Request $request)
    {
        // Obtener el número de adultos de la solicitud
        $numAdultos = $request->query('numAdultos');

        // Buscar habitaciones basadas en la capacidad para adultos y el hotelID
        $habitacionesDisponibles = Habitacion::where('hotelID', $hotelID)
            ->where('tipohabitacion', '=', $numAdultos) // Asegurarse de que la capacidad de la habitación es suficiente
            ->get();

        // Obtener solo los IDs de las habitaciones disponibles
        $habitacionIDs = $habitacionesDisponibles->pluck('habitacionID');

        // Verificar si hay habitaciones disponibles
        if ($habitacionIDs->isEmpty()) {
            return response()->json(['mensaje' => 'No hay habitaciones disponibles'], 404);
        }

        // Devolver los IDs de las habitaciones en formato JSON
        return response()->json(['habitacionID' => $habitacionIDs->first()]);
    }

    public function generarPDF($reservaID)
    {
        // Consulta para generar un pdf de la reserva específica
        $reserva = Reserva::join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select(
                'reservas.*',
                'hoteles.nombre as nombre_hotel',
                'habitaciones.numhabitacion',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellidos) as nombre_completo")
            )
            ->where('reservas.reservaID', $reservaID) // Filtrar por reservaID
            ->first();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de los datos
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos Reserva', 0, 1, 'C');

        // Crear el contenido en el PDF
        $html = '<div style="font-size: 12px; text-align: center;">';

        // Agregar los datos de la reserva al contenido del PDF
        if ($reserva) {
            $html .= '<div style="margin-bottom: 20px;">';
            $html .= '<strong>ID Reserva:</strong> ' . $reserva->reservaID . '<br>';
            $html .= '<strong>Nombre Cliente:</strong> ' . $reserva->nombre_completo . '<br>';
            $html .= '<strong>Nombre Hotel:</strong> ' . $reserva->nombre_hotel . '<br>';
            $html .= '<strong>Fecha inicio reserva:</strong> ' . $reserva->fechainicio . '<br>';
            $html .= '<strong>Fecha fin reserva:</strong> ' . $reserva->fechafin . '<br>';
            $html .= '<strong>Nº Habitación:</strong> ' . $reserva->numhabitacion . '<br>';
            $html .= '<strong>Precio Total Reserva:</strong> ' . $reserva->preciototal . '<br>';
            $html .= '<strong>Nº Adultos:</strong> ' . $reserva->num_adultos . '<br>';
            $html .= '<strong>Nº de niños:</strong> ' . $reserva->num_ninos . '<br>';
            $html .= '</div>';
        } else {
            $html .= '<div>No se encontraron datos para la reserva especificada.</div>';
        }

        $html .= '</div>';

        // Agregar el contenido al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "reserva_" . $reservaID . ".pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function generarPDFTotal()
    {
        // Consulta para generar un pdf del listado de reservas
        $reservas = Reserva::join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select(
                'reservas.*',
                'hoteles.nombre as nombre_hotel',
                'habitaciones.numhabitacion',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellidos) as nombre_completo")
            )
            ->get();


        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de la tabla
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos Reservas', 0, 1, 'C');

        // Crear la tabla en el PDF 
        $html = '<table border="1" style="font-size: 10px;">';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th>ID Reserva</th>';
        $html .= '<th>Nombre Cliente</th>';
        $html .= '<th>Nombre Hotel</th>';
        $html .= '<th>Fecha inicio reserva</th>';
        $html .= '<th>Fecha fin reserva</th>';
        $html .= '<th>Nº Habitación</th>';
        $html .= '<th>Precio Total Reserva</th>';
        $html .= '<th>Nº Adultos</th>';
        $html .= '<th>Nº de niños</th>';
        $html .= '</tr>';

        // Iterar sobre los datos de las reservas y agregarlos a la tabla del PDF
        foreach ($reservas as $reserva) {
            $html .= '<tr>';
            $html .= '<td>' . $reserva->reservaID . '</td>'; // ID de la reserva
            $html .= '<td>' . $reserva->nombre_completo . '</td>'; // Nombre del cliente
            $html .= '<td>' . $reserva->nombre_hotel . '</td>'; // Nombre del hotel
            $html .= '<td>' . $reserva->fechainicio . '</td>'; // Fecha de inicio
            $html .= '<td>' . $reserva->fechafin . '</td>'; // Fecha de fin
            $html .= '<td>' . $reserva->numhabitacion . '</td>'; // Número de habitación 
            $html .= '<td>' . $reserva->preciototal . '</td>'; // Precio total
            $html .= '<td>' . $reserva->num_adultos . '</td>'; // Número de adultos
            $html .= '<td>' . $reserva->num_ninos . '</td>'; // Número de niños
            $html .= '</tr>';
        }


        $html .= '</table>';

        // Agregar la tabla al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "lista_reservas.pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function buscarReservas(Request $request)
    {
        $query = $request->input('query');

        // Consulta para utilizar el buscador en el listado de reservas
        $consulta = Reserva::select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion', 'hoteles.nombre as hotel_nombre') // Selecciona todas las columnas de reservas
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID') // Une con la tabla de clientes
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') // Une con la tabla de habitaciones
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID') // Une con la tabla de hoteles
            ->where('reservas.reservaID', 'LIKE', "%$query%")
            ->orWhere('clientes.nombre', 'LIKE', "%$query%")
            ->orWhere('clientes.apellidos', 'LIKE', "%$query%")
            ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
            ->orWhere('reservas.num_adultos', 'LIKE', "%$query%")
            ->orWhere('reservas.num_ninos', 'LIKE', "%$query%");


        $totalReservas = $consulta->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalReservas / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $reservas = $consulta->skip($inicio)->take($registros_por_pagina)->get();

        return view('listarreservas', [
            'reserva' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ], compact('reservas'));
    }
}
