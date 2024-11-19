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

class ListadoCheckinControlador extends Controller
{
    public function listadoCheckin(Request $request)
    {
        // Obtener el valor del query 
        $queryParam = $request->input('query', '');

        // Obtener la fecha actual
        $fecha_actual = Carbon::now()->startOfDay();

        // Consulta para mostrar el listado de check-in
        $query = DB::table('reservas')
            ->join('habitaciones', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion', 'hoteles.nombre as hotel_nombre')
            ->where('reservas.fecha_checkin', '>=', $fecha_actual); // Filtrar por fecha

        // Aplicar el filtro de búsqueda si se proporciona
        if ($queryParam) {
            $query->where(function ($q) use ($queryParam) {
                $q->where('reservas.reservaID', 'LIKE', "%$queryParam%")
                    ->orWhere('clientes.nombre', 'LIKE', "%$queryParam%")
                    ->orWhere('clientes.apellidos', 'LIKE', "%$queryParam%")
                    ->orWhere('habitaciones.numhabitacion', 'LIKE', "%$queryParam%")
                    ->orWhere('hoteles.nombre', 'LIKE', "%$queryParam%");
            });
        }

        // Contar el total de reservas después de aplicar el filtro
        $totalReservas = $query->count();

        // Configurar la paginación
        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalReservas / $registros_por_pagina);

        // Ajustar la página actual
        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        // Calcular el inicio de la paginación
        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        // Obtener las reservas paginadas
        $reservas = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina,
            'fecha_actual' => $fecha_actual->toDateTimeString(),
            'query' => $queryParam
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listadocheckin', $parametros);
    }



    public function buscarCheckin(Request $request)
    {
        $query = $request->input('query', '');

        // Obtener la fecha actual
        $fecha_actual = Carbon::now()->startOfDay();

        // Realiza la consulta para cuando se usa el buscador
        $consulta = Reserva::select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->where('reservas.fecha_checkin', '>=', $fecha_actual) // Filtrar por fecha
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('reservas.reservaID', 'LIKE', "%$query%")
                    ->orWhere('clientes.nombre', 'LIKE', "%$query%")
                    ->orWhere('clientes.apellidos', 'LIKE', "%$query%")
                    ->orWhere('habitaciones.numhabitacion', 'LIKE', "%$query%")
                    ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
                    ->orWhere('reservas.num_adultos', 'LIKE', "%$query%")
                    ->orWhere('reservas.num_ninos', 'LIKE', "%$query%");
            });

        // Contar total de reservas aplicando el filtro
        $totalReservas = $consulta->count();

        // Paginación
        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalReservas / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        // Obtener las reservas para la página actual aplicando la paginación
        $reservas = $consulta->skip($inicio)->take($registros_por_pagina)->get();

        return view('listadocheckin', [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina,
            'fecha_actual' => $fecha_actual->toDateTimeString(),
            'query' => $query
        ]);
    }

    public function mostrarCheckin(Request $request, $reservaID)
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

        return view('registrarcheckin', $parametros);
    }
    public function registrarCheckin(Request $request, $reservaID)
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
                'fechaCheckin' => 'required|date|after:fechainicio', // Verifica que sea una fecha válida y después de la fecha de entrada
            ]);

            $fechaCheckinNueva = Carbon::parse($validatedData['fechaCheckin']);

            // Comparar con la fecha de check-in actual, para actualizar solo si es necesario
            if ($reserva->fecha_checkin !== $fechaCheckinNueva->toDateString()) {
                // Actualizar solo la fecha de check-in
                $reserva->fecha_checkin = $fechaCheckinNueva;
                $reserva->fechainicio = $fechaCheckinNueva; // `fechainicio` debe ser igual a `fecha_checkin`
                $reserva->save();
            }

            // Recargar la reserva para obtener los datos actualizados
            $reserva->refresh();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'La fecha de check-in se ha actualizado correctamente.',
                    'reserva' => $reserva
                ]);
            }

            return redirect()->route('listarReservas')->with('status', 'La fecha de check-in se ha actualizado correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al actualizar la fecha de check-in', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al actualizar la fecha de check-in')->withInput();
        }
    }
}
