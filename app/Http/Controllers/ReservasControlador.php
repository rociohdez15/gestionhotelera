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
    
    if (!$clienteID) {
        $clienteID = Auth::id();
    }
    
    $reservas = DB::table('reservas') 
        ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') 
        ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID') 
        ->leftJoin('imagenes_hoteles', function ($hoteles) { 
            $hoteles->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID') 
                ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%');
        })
        ->leftJoin('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') 
        ->leftJoin('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') 
        ->join('users', 'reservas.clienteID', '=', 'users.id') 
        ->select(
            'reservas.*', 
            'hoteles.nombre as hotel_nombre', 
            'imagenes_hoteles.imagen as hotel_imagen', 
            DB::raw('GROUP_CONCAT(servicios.nombre, " -> ", DATE_FORMAT(servicios.horario, "%Y-%m-%d %H:%i") SEPARATOR ", ") as servicio_detalles'), 
            'users.name as cliente_nombre' 
        )
        ->where('reservas.clienteID', $clienteID) 
        ->groupBy('reservas.reservaID', 'hoteles.nombre', 'imagenes_hoteles.imagen', 'users.name') 
        ->get();

    
    foreach ($reservas as $reserva) {
        $fechaEntrada = Carbon::parse($reserva->fechainicio);
        $fechaSalida = Carbon::parse($reserva->fechafin);
        $numDias = $fechaEntrada->diffInDays($fechaSalida);
        $reserva->num_dias = $numDias;
    }

    
    $totalReservas = $reservas->count();
    $registros_por_pagina = 3;
    $pagina_actual = $request->input('pagina', 1);
    $total_paginas = ceil($totalReservas / $registros_por_pagina);

    
    if ($pagina_actual < 1) $pagina_actual = 1;
    if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

    
    $inicio = ($pagina_actual - 1) * $registros_por_pagina;

    
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
