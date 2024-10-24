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

        return view('listarreservas', [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ]);
    }

    public function delReserva($reservaID)
    {
        $reserva = Reserva::find($reservaID);

        if (!$reserva) {
            return back()->withError('La reserva especificada no existe.');
        }

        $reserva->delete();

        return redirect()->route('listarReservas')->with('status', 'La reserva se ha eliminado correctamente.');
    }

    public function mostrarReserva($reservaID)
    {

        $reserva = Reserva::find($reservaID);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }

        $edadesNinos = DB::table('edadesninos')->where('reservaID', $reservaID)->get();

        $habitacion = DB::table('habitaciones')->where('habitacionID', $reserva->habitacionID)->first();
        if (!$habitacion) {
            return response()->json(['error' => 'Habitación no encontrada'], 404);
        }

        $hotel = DB::table('hoteles')->where('hotelID', $habitacion->hotelID)->first();
        if (!$hotel) {
            return response()->json(['error' => 'Hotel no encontrado'], 404);
        }

        $cliente = DB::table('clientes')->where('clienteID', $reserva->clienteID)->first();
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return view('editarreserva', compact('reserva', 'edadesNinos', 'hotel', 'cliente', 'habitacion'));
    }


    public function editarReserva(Request $request, $reservaID)
{
    $reserva = Reserva::find($reservaID);
    if (!$reserva) {
        Log::error('Reserva no encontrada', ['reservaID' => $reservaID]);
        return response()->json(['message' => 'Reserva no encontrada'], 404);
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

        // Calcular la diferencia de días
        $fechaEntradaOriginal = Carbon::parse($reserva->fechainicio);
        $fechaSalidaOriginal = Carbon::parse($reserva->fechafin);
        $fechaEntradaNueva = Carbon::parse($validatedData['fechaEntrada']);
        $fechaSalidaNueva = Carbon::parse($validatedData['fechaSalida']);

        $diasOriginales = $fechaSalidaOriginal->diffInDays($fechaEntradaOriginal);
        $diasNuevos = $fechaSalidaNueva->diffInDays($fechaEntradaNueva);

        // Calcular la diferencia de adultos y niños
        $diferenciaAdultos = $validatedData['numAdultos'] - $reserva->num_adultos;
        $diferenciaNinos = $validatedData['numNinos'] - $reserva->num_ninos;

        // Calcular el ajuste de precio por cambios
        $ajustePrecio = ($diferenciaAdultos * 20) + ($diferenciaNinos * 10);

        // Calcular el precio basado en días, asegurando que no baje de 60€ por día
        if ($diasNuevos !== $diasOriginales) {
            // Asegurar que el precio total no sea inferior al original
            $ajustePrecio += 200;
        }

        // Ajustar el precio total
        $reserva->preciototal += $ajustePrecio;

        // Actualizar los datos de la reserva
        $reserva->fechainicio = $validatedData['fechaEntrada'];
        $reserva->fechafin = $validatedData['fechaSalida'];
        $reserva->num_adultos = $validatedData['numAdultos'];
        $reserva->num_ninos = $validatedData['numNinos'];
        $reserva->habitacionID = $validatedData['habitacionID'];
        $reserva->save();

        // Eliminar las edades anteriores
        EdadNino::where('reservaID', $reserva->reservaID)->delete();

        // Guardar las nuevas edades de los niños
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

        return response()->json(['message' => 'La reserva se ha editado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al editar la reserva', 'error' => $e->getMessage()], 500);
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
    
}
