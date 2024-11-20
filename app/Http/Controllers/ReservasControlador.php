<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservasControlador extends Controller
{
public function mostrarMisReservas(Request $request, $clienteID  = null)
{
    // Si no se proporciona un clienteID, usa el ID del usuario autenticado
    if (!$clienteID) {
        $clienteID = Auth::id();
    }
    // Recuperar todas las reservas del cliente con sus servicios adicionales
    $reservas = DB::table('reservas') //Selecciona la tabla reservas
        ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') //Une la tabla habitaciones con la tabla reservas
        ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID') // Une la tabla hoteles con la tabla habitaciones
        ->leftJoin('imagenes_hoteles', function ($hoteles) { // Realiza un left join con la tabla 'imagenes_hoteles'
            $hoteles->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID') // Selecciona las imagenes del hotel con dicha ruta
                ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%');
        })
        ->leftJoin('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') // Une la tabla reservas_Servicios con la tabla reservas
        ->leftJoin('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') // Une la tabla reserva_servicios con la tabla servicios
        ->join('users', 'reservas.clienteID', '=', 'users.id') // Une el clienteID de la tabla reservas con el ID del usuario
        ->select(
            'reservas.*', // Selecciona todas las columnas de la tabla 'reservas'
            'hoteles.nombre as hotel_nombre', //Selecciona el nombre del hotel
            'imagenes_hoteles.imagen as hotel_imagen', //Selecciona la imagen del hotel
            DB::raw('GROUP_CONCAT(servicios.nombre, " -> ", DATE_FORMAT(servicios.horario, "%Y-%m-%d %H:%i") SEPARATOR ", ") as servicio_detalles'), //Concatenar el nombre del servicio con el horario
            'users.name as cliente_nombre' //Selecciona el nombre del cliente que ha hecho la reserva
        )
        ->where('reservas.clienteID', $clienteID) // Filtra las reservas para que solo incluya las que pertenecen al cliente
        ->groupBy('reservas.reservaID', 'hoteles.nombre', 'imagenes_hoteles.imagen', 'users.name') // Agrupar por reservaID
        ->get();

    //Calcula el numero de dias que tiene la reserva
    foreach ($reservas as $reserva) {
        $fechaEntrada = Carbon::parse($reserva->fechainicio);
        $fechaSalida = Carbon::parse($reserva->fechafin);
        $numDias = $fechaEntrada->diffInDays($fechaSalida);
        $reserva->num_dias = $numDias;
    }

    // Total de reservas
    $totalReservas = $reservas->count();
    $registros_por_pagina = 3;
    $pagina_actual = $request->input('pagina', 1);
    $total_paginas = ceil($totalReservas / $registros_por_pagina);

    // Validar la página actual
    if ($pagina_actual < 1) $pagina_actual = 1;
    if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

    // Calcular el índice de inicio
    $inicio = ($pagina_actual - 1) * $registros_por_pagina;

    // Obtener datos paginados
    $datos_paginados = $reservas->slice($inicio, $registros_por_pagina)->values();

    $parametros = [
        "mensajes" => [],
        "datos" => $datos_paginados,
        "pagina_actual" => $pagina_actual,
        "total_paginas" => $total_paginas,
        "registros_por_pagina" => $registros_por_pagina,
    ];

    if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json($parametros);
    }

    return view('misreservas', $parametros);
}
}
