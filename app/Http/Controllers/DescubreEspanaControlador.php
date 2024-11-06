<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescubreEspanaControlador extends Controller
{
    public function descubreEspana(Request $request, $ciudad)
    {
        // Obtener los hoteles de la ciudad con su imagen de portada
        $hotelesQuery = DB::table('hoteles')
            ->leftJoin('imagenes_hoteles', function ($join) {
                $join->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%')
                    ->whereRaw('imagenes_hoteles.hotelID = (
                        SELECT MIN(hotelID)
                        FROM imagenes_hoteles
                        WHERE hotelID = hoteles.hotelID
                        AND imagen LIKE "images/portadas/portada%"
                    )');
            })
            ->where('hoteles.ciudad', $ciudad)
            ->select('hoteles.*', 'imagenes_hoteles.imagen as imagen_url');

        // Obtener todos los hoteles y contar el total de alojamientos
        $hoteles = $hotelesQuery->get();
        $totalAlojamientos = $hoteles->count();

        // Define los parámetros de paginación
        $registros_por_pagina = 5; 
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalAlojamientos / $registros_por_pagina);

        // Validar la página actual
        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        // Calcular el índice de inicio
        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        // Obtener los datos para la página actual utilizando slice()
        $datos_paginados = $hoteles->slice($inicio, $registros_por_pagina)->values(); 

        $parametros = [
            "tituloventana" => "AlojaDirecto | Descubre España",
            "datos" => $datos_paginados,
            "mensajes" => [],
            "totalAlojamientos" => $totalAlojamientos,
            "ubicacion" => $ciudad,
            "pagina_actual" => $pagina_actual,
            "total_paginas" => $total_paginas,
            "registros_por_pagina" => $registros_por_pagina,
        ];

        return view('descubreespana', $parametros); // Muestra la vista de descubre España
    }
}
