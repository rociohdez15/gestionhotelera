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

class ListarReservasControlador extends Controller
{
    public function listarReservas(Request $request)
    {

        $query = DB::table('reservas')
            ->join('habitaciones', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion', 'hoteles.nombre as hotel_nombre');

        $totalReservas = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalReservas / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $reservas = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarreservas', $parametros);
    }

    public function actualizarReservas()
    {
        $reservas = DB::table('reservas')
            ->join('habitaciones', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion', 'hoteles.nombre as hotel_nombre')
            ->paginate(5);

        return response()->json($reservas);
    }

    public function delReserva($reservaID, Request $request)
    {
        
        $reserva = Reserva::find($reservaID);

        if (!$reserva) {
            return back()->withError('La reserva especificada no existe.');
        }

        
        DB::table('edadesninos')->where('reservaID', $reservaID)->delete();

        
        $serviciosIDs = DB::table('reservas_servicios')
            ->where('reservaID', $reservaID)
            ->pluck('servicioID');

        
        $serviciosEliminados = DB::table('servicios')
            ->whereIn('servicioID', $serviciosIDs)
            ->get();

        
        DB::table('reservas_servicios')->where('reservaID', $reservaID)->delete();

        
        DB::table('servicios')
            ->whereIn('servicioID', $serviciosIDs)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reservas_servicios')
                    ->whereColumn('reservas_servicios.servicioID', 'servicios.servicioID');
            })
            ->delete();

        
        $reserva->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'La reserva se ha eliminado correctamente.',
                'reserva' => $reserva,
                'serviciosEliminados' => $serviciosEliminados
            ]);
        }

        return redirect()->route('listarReservas')->with('status', 'La reserva se ha eliminado correctamente.');
    }

    public function mostrarReserva(Request $request, $reservaID)
    {
        $reserva = Reserva::find($reservaID);

        if (!$reserva) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Reserva no encontrada'], 404);
            }
            return back()->withError('Reserva no encontrada');
        }

        $edadesNinos = DB::table('edadesninos')->where('reservaID', $reservaID)->get();

        $habitacion = DB::table('habitaciones')->where('habitacionID', $reserva->habitacionID)->first();
        if (!$habitacion) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Habitación no encontrada'], 404);
            }
            return back()->withError('Habitación no encontrada');
        }

        $hotel = DB::table('hoteles')->where('hotelID', $habitacion->hotelID)->first();
        if (!$hotel) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Hotel no encontrado'], 404);
            }
            return back()->withError('Hotel no encontrado');
        }

        $cliente = DB::table('clientes')->where('clienteID', $reserva->clienteID)->first();
        if (!$cliente) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }
            return back()->withError('Cliente no encontrado');
        }

        $parametros = compact('reserva', 'edadesNinos', 'hotel', 'cliente', 'habitacion');

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('editarreserva', $parametros);
    }

    public function editarReserva(Request $request, $reservaID)
    {
        $reserva = Reserva::find($reservaID);
        if (!$reserva) {
            Log::error('Reserva no encontrada', ['reservaID' => $reservaID]);
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Reserva no encontrada'], 404);
            }
            return back()->withError('Reserva no encontrada');
        }

        try {
            $validatedData = $request->validate([
                'fechaEntrada' => 'required|date',
                'fechaSalida' => 'required|date|after:fechaEntrada',
                'numAdultos' => 'required|integer|min:1',
                'numNinos' => 'nullable|integer|min:0',
                'edadesNinos.*' => 'nullable|integer|min:0',
                'habitacionID' => 'required|integer',
            ]);

            
            $fechaEntradaOriginal = Carbon::parse($reserva->fechainicio);
            $fechaSalidaOriginal = Carbon::parse($reserva->fechafin);
            $fechaEntradaNueva = Carbon::parse($validatedData['fechaEntrada']);
            $fechaSalidaNueva = Carbon::parse($validatedData['fechaSalida']);

            $diasOriginales = $fechaSalidaOriginal->diffInDays($fechaEntradaOriginal);
            $diasNuevos = $fechaSalidaNueva->diffInDays($fechaEntradaNueva);

            
            $diferenciaAdultos = $validatedData['numAdultos'] - $reserva->num_adultos;
            $diferenciaNinos = $validatedData['numNinos'] - $reserva->num_ninos;

            
            $ajustePrecio = ($diferenciaAdultos * 20) + ($diferenciaNinos * 10);

            
            if ($diasNuevos !== $diasOriginales) {
                
                $ajustePrecio += 200;
            }

            
            $reserva->preciototal += $ajustePrecio;

            
            $reserva->fechainicio = $validatedData['fechaEntrada'];
            $reserva->fechafin = $validatedData['fechaSalida'];
            $reserva->fecha_checkin = $validatedData['fechaEntrada'];
            $reserva->fecha_checkout = $validatedData['fechaSalida'];
            $reserva->num_adultos = $validatedData['numAdultos'];
            $reserva->num_ninos = $validatedData['numNinos'];
            $reserva->habitacionID = $validatedData['habitacionID'];
            $reserva->save();

            
            EdadNino::where('reservaID', $reserva->reservaID)->delete();

            
            if (!empty($validatedData['edadesNinos'])) {
                foreach ($validatedData['edadesNinos'] as $edad) {
                    if (is_numeric($edad) && $edad >= 0) {
                        EdadNino::create([
                            'edad' => $edad,
                            'reservaID' => $reserva->reservaID,
                        ]);
                    }
                }
            }

            
            $reserva->refresh();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'La reserva se ha editado correctamente.',
                    'reserva' => $reserva
                ]);
            }

            return response()->json(['success' => 'La reserva se ha editado correctamente']);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al editar la reserva', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al editar la reserva')->withInput();
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
        
        $numAdultos = $request->query('numAdultos');

        
        $habitacionesDisponibles = Habitacion::where('hotelID', $hotelID)
            ->where('tipohabitacion', '=', $numAdultos) 
            ->get();

        
        $habitacionIDs = $habitacionesDisponibles->pluck('habitacionID');

        
        if ($habitacionIDs->isEmpty()) {
            return response()->json(['mensaje' => 'No hay habitaciones disponibles'], 404);
        }

        
        return response()->json(['habitacionID' => $habitacionIDs->first()]);
    }

    public function generarPDF($reservaID)
    {
        
        $reserva = Reserva::join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select(
                'reservas.*',
                'hoteles.nombre as nombre_hotel',
                'habitaciones.numhabitacion',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellidos) as nombre_completo")
            )
            ->where('reservas.reservaID', $reservaID) 
            ->first();

        
        $pdf = new TCPDF();

        
        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos Reserva', 0, 1, 'C');

        
        $html = '<div style="font-size: 12px; text-align: center;">';

        
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

        
        $pdf->writeHTML($html, true, false, true, false, '');

        
        $filename = "reserva_" . $reservaID . ".pdf";

        
        $pdf->Output($filename, 'D');
    }

    public function generarPDFTotal()
    {
        
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


        
        $pdf = new TCPDF();

        
        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos Reservas', 0, 1, 'C');

        
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

        
        foreach ($reservas as $reserva) {
            $html .= '<tr>';
            $html .= '<td>' . $reserva->reservaID . '</td>'; 
            $html .= '<td>' . $reserva->nombre_completo . '</td>'; 
            $html .= '<td>' . $reserva->nombre_hotel . '</td>'; 
            $html .= '<td>' . $reserva->fechainicio . '</td>'; 
            $html .= '<td>' . $reserva->fechafin . '</td>'; 
            $html .= '<td>' . $reserva->numhabitacion . '</td>'; 
            $html .= '<td>' . $reserva->preciototal . '</td>'; 
            $html .= '<td>' . $reserva->num_adultos . '</td>'; 
            $html .= '<td>' . $reserva->num_ninos . '</td>'; 
            $html .= '</tr>';
        }


        $html .= '</table>';

        
        $pdf->writeHTML($html, true, false, true, false, '');

        
        $filename = "lista_reservas.pdf";

        
        $pdf->Output($filename, 'D');
    }

    public function buscarReservas(Request $request)
    {
        $query = $request->input('query');

        
        $consulta = Reserva::select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion', 'hoteles.nombre as hotel_nombre') 
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID') 
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') 
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID') 
            ->where('reservas.reservaID', 'LIKE', "%$query%")
            ->orWhere('clientes.nombre', 'LIKE', "%$query%")
            ->orWhere('clientes.apellidos', 'LIKE', "%$query%")
            ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
            ->orWhere('reservas.num_adultos', 'LIKE', "%$query%")
            ->orWhere('habitaciones.numhabitacion', 'LIKE', "%$query%");

        $reservas = $consulta->orderBy('reservas.reservaID', 'asc')->paginate(5);

        if ($request->ajax()) {
            return response()->json($reservas);
        }

        return view('listarreservas', compact('reservas'));
    }
}
