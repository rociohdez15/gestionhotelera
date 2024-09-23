<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\Resena;
use Carbon\Carbon;


class ResenasControlador extends Controller
{
    //Método que mostrará los hoteles que no han recibido la reseña
    public function dejarResenas(Request $request)
{
    // Obtener el cliente autenticado
    $clienteID = Auth::id();

    // Filtrar hoteles de reservas donde el cliente aún no ha dejado una reseña
    $hotelesSinResena = Hotel::select('hoteles.*', DB::raw('(SELECT imagen FROM imagenes_hoteles WHERE hotelID = hoteles.hotelID AND imagen LIKE "images/portadas/portada%" LIMIT 1) as imagen_url'))
        ->whereIn('hotelID', function ($habitaciones) use ($clienteID) {  // Filtra los hoteles cuyo 'hotelID' está en la tabla habitaciones
            $habitaciones->select('habitaciones.hotelID') //Selecciona el hotelID de la tabla habitaciones
                  ->from('reservas') // Selecciona la tabla reservas 
                  ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') // Une la tabla reservas con la tabla habitaciones
                  ->where('reservas.clienteID', $clienteID) // Filtra las reservas para el cliente autenticado
                  ->whereNotExists(function ($resenas) use ($clienteID) { // Comprueba que no existan reseñas para el hotel
                      $resenas->select(DB::raw(1)) //Verifica si hay al menos una fila que contenga dichas restricciones
                              ->from('resenas') // Consulta la tabla 'resenas'
                              ->whereColumn('resenas.hotelID', 'habitaciones.hotelID') // Asegura que el 'hotelID' en 'resenas' coincida con el de 'habitaciones'
                              ->where('resenas.clienteID', $clienteID); // Filtra para el cliente específico
                  });
        })
        ->get();

    // Cuenta el número total de hoteles sin reseña
    $totalHoteles = $hotelesSinResena->count();

    // Define los parámetros de paginación
    $registros_por_pagina = 5; // Muestra 1 registro por página
    $pagina_actual = $request->input('pagina', 1);
    $total_paginas = ceil($totalHoteles / $registros_por_pagina);

    // Valida la página actual
    if ($pagina_actual < 1) $pagina_actual = 1;
    if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

    // Calcula el índice de inicio
    $inicio = ($pagina_actual - 1) * $registros_por_pagina;

    // Obtiene los datos para la página actual
    $datos_paginados = $hotelesSinResena->slice($inicio, $registros_por_pagina);

    // Cambia la colección a una nueva colección para no perder la información de paginación
    $datos = $datos_paginados->values(); 

    $parametros = [
        "datos" => $datos,
        "pagina_actual" => $pagina_actual,
        "total_paginas" => $total_paginas,
        "registros_por_pagina" => $registros_por_pagina,
    ];

    return view('dejaresena', compact('hotelesSinResena'), $parametros);
}


}    
