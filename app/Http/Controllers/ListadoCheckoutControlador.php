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

class ListadoCheckoutControlador extends Controller
{
    public function listadoCheckout(Request $request)
    {
        // Obtener el valor del query desde el request
        $queryParam = $request->input('query', ''); // Por defecto vacío

        // Obtener la fecha actual
        $fecha_actual = Carbon::now()->startOfDay();

        // Construir la consulta
        $query = DB::table('reservas')
            ->join('habitaciones', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion', 'hoteles.nombre as hotel_nombre')
            ->where('reservas.fecha_checkout', '>=', $fecha_actual); // Filtrar por fecha

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

        return view('listadocheckout', [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina,
            'fecha_actual' => $fecha_actual->toDateTimeString(), // Pasar fecha actual
            'query' => $queryParam // Asegúrate de pasar el query a la vista
        ]);
    }



    public function buscarCheckout(Request $request)
    {
        $query = $request->input('query', '');

        // Obtener la fecha actual
        $fecha_actual = Carbon::now()->startOfDay();

        // Realiza la consulta con uniones a las tablas necesarias
        $consulta = Reserva::select('reservas.*', 'clientes.nombre', 'clientes.apellidos', 'habitaciones.numhabitacion')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->where('reservas.fecha_checkout', '>=', $fecha_actual) // Filtrar por fecha
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

        return view('listadocheckout', [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina,
            'fecha_actual' => $fecha_actual->toDateTimeString(), // Pasar fecha actual
            'query' => $query
        ]);
    }

    public function mostrarCheckout($reservaID)
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

        return view('registrarcheckout', compact('reserva', 'edadesNinos', 'hotel', 'cliente', 'habitacion'));
    }

    public function registrarCheckout(Request $request, $reservaID)
{
    $reserva = Reserva::find($reservaID);
    if (!$reserva) {
        Log::error('Reserva no encontrada', ['reservaID' => $reservaID]);
        return response()->json(['message' => 'Reserva no encontrada'], 404);
    }

    try {
        $validatedData = $request->validate([
            'fechaCheckout' => 'required|date|after:fechainicio', // Verifica que sea una fecha válida y después de la fecha de entrada
        ]);

        $fechaCheckoutNueva = Carbon::parse($validatedData['fechaCheckout']);

        // Comparar con la fecha de checkout actual, para actualizar solo si es necesario
        if ($reserva->fecha_checkout !== $fechaCheckoutNueva->toDateString()) {
            // Actualizar solo la fecha de checkout
            $reserva->fecha_checkout = $fechaCheckoutNueva;
            $reserva->fechafin = $fechaCheckoutNueva; // Sincronizar si `fechafin` debe ser igual a `fecha_checkout`
            $reserva->save();
        }

        return response()->json(['message' => 'La fecha de checkout se ha actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al actualizar la fecha de checkout', 'error' => $e->getMessage()], 500);
    }
}


}
