<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuscarHotelesControlador extends Controller
{
    //Método principar que mostrará los hoteles disponibles según el tipo de búsqueda realizada.
    public function buscarhoteles(Request $request)
    {
        //Obtener los parámetros de la búsqueda
        $ubicacion = $request->input('ubicacion');
        $fechaEntrada = $request->input('fecha_entrada');
        $fechaSalida = $request->input('fecha_salida');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $totalPersonas = $adultos + $ninos;
        $habitacionesSolicitadas = $request->input('habitaciones');

        // Se hace la consulta prinicipal para obtener los datos de los hoteles y su imagen de portada
        $hoteles = DB::table('hoteles')
            ->leftJoin('imagenes_hoteles', function ($join) {
                // Une la tabla de hoteles con la tabla de imágenes
                $join->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    // Filtra las imágenes para seleccionar sólo las que comienzan por 'portada'
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%') //En la BD se guardarán las imagenes de portadas de los hoteles con esa ruta para diferenciarlas
                    //Subconsulta para seleccionar solo una imagen por hotel (para la portada)
                    ->whereRaw('imagenes_hoteles.hotelID = (
                         SELECT MIN(hotelID)
                         FROM imagenes_hoteles
                         WHERE hotelID = hoteles.hotelID
                           AND imagen LIKE "images/portadas/portada%"
                     )');
            })
            ->select('hoteles.*', 'imagenes_hoteles.imagen as imagen_url');

        // Aplica filtros según los parámetros de búsqueda
        if (!empty($ubicacion)) {
            $hoteles->where(function ($query) use ($ubicacion) {
                // Filtra hoteles por ciudad o nombre que contengan el campo ubicación
                $query->where('hoteles.ciudad', 'like', '%' . $ubicacion . '%')
                    ->orWhere('hoteles.nombre', 'like', '%' . $ubicacion . '%');
            });
        }

        // Obtiene todos los hoteles y filtra aquellos que tienen habitaciones disponibles
        $hoteles = $hoteles->get()->filter(function ($hotel) use ($totalPersonas, $habitacionesSolicitadas, $fechaEntrada, $fechaSalida) {
            // Consulta para obtener habitaciones disponibles teniendo en cuenta las reservas hechas
            $habitaciones = DB::table('habitaciones')
                ->leftJoin('reservas', function ($join) use ($fechaEntrada, $fechaSalida) {
                    // Une la tabla de habitaciones con la tabla de reservas
                    $join->on('habitaciones.habitacionID', '=', 'reservas.habitacionID')
                        ->where(function ($query) use ($fechaEntrada, $fechaSalida) {
                            // Filtra reservas que coincidan con el rango de fechas solicitado
                            $query->whereBetween('reservas.fechainicio', [$fechaEntrada, $fechaSalida])
                                ->orWhereBetween('reservas.fechafin', [$fechaEntrada, $fechaSalida])
                                ->orWhere(function ($query) use ($fechaEntrada, $fechaSalida) {
                                    // Consulta donde la reserva dura todo el rango de fechas solicitado
                                    $query->where('reservas.fechainicio', '<=', $fechaEntrada)
                                        ->where('reservas.fechafin', '>=', $fechaSalida);
                                });
                        });
                })
                //Filtra habitaciones que no tienen reservas en el rango de fechas
                ->whereNull('reservas.habitacionID')
                //Filtra por el hotelID del hotel actual
                ->where('habitaciones.hotelID', $hotel->hotelID)
                ->get();

            // Obtiene combinaciones válidas de habitaciones para el hotel actual
            $combinacionesValidas = $this->obtenerCombinacionesHabitaciones($habitaciones, $totalPersonas, $habitacionesSolicitadas);

            //Si se encuentran combinaciones de habitaciones válidas, se muestran 
            if ($combinacionesValidas) {
                $hotel->habitaciones = $combinacionesValidas;
                return true;
            }

            //Descarta el hotel si no se encontraron combinaciones válidas
            return false;
        });

        // Cuenta el número total de alojamientos que cumplen con las características indicadas en la búsqueda
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

        // Obtener los datos para la página actual
        $datos_paginados = $hoteles->slice($inicio, $registros_por_pagina);

        // Prepara los parámetros para pasar a la vista
        $parametros = [
            "tituloventana" => "AlojaDirecto | Buscar Hotel",
            "datos" => $datos_paginados,
            "mensajes" => [],
            "totalAlojamientos" => $totalAlojamientos,
            "ubicacion" => $ubicacion,
            "pagina_actual" => $pagina_actual,
            "total_paginas" => $total_paginas,
            "registros_por_pagina" => $registros_por_pagina,
        ];

        return view('buscarhoteles', $parametros); // Muestra la vista de listar hoteles
    }

    // Método privado para obtener combinaciones válidas de habitaciones
    private function obtenerCombinacionesHabitaciones($habitaciones, $totalPersonas, $habitacionesSolicitadas)
    {
        $result = null;

        // Llama al método que encuentra combinaciones válidas de habitaciones
        $this->encontrarHabitaciones($habitaciones, $totalPersonas, $habitacionesSolicitadas, [], $result);

        return $result;
    }

    // Método privado que encuentra combinaciones válidas de habitaciones usando búsqueda completa
    private function encontrarHabitaciones($habitaciones, $personasRestantes, $habitacionesSolicitadas, $combinacionActual, &$result)
    {
        // Si se cumple el número de personas y el número de habitaciones solicitadas
        if ($personasRestantes <= 0 && count($combinacionActual) == $habitacionesSolicitadas) {
            $result = $combinacionActual; // Asignamos la combinación encontrada como resultado
            return;
        }

        // Si se excede el número de habitaciones solicitadas o si ya tenemos una combinación válida, detén la búsqueda
        if (count($combinacionActual) >= $habitacionesSolicitadas || $result) {
            return;
        }

        // Recorre todas las habitaciones disponibles
        foreach ($habitaciones as $index => $habitacion) {
            // Si la habitación ya está en la combinación actual, continúa con la siguiente
            if (in_array($habitacion, $combinacionActual)) {
                continue;
            }

            // Calcula la capacidad de la habitación
            $capacidadHabitacion = $habitacion->tipohabitacion; 

            // Verifica si la capacidad de la habitación es suficiente para las personas restantes
            if ($capacidadHabitacion > $personasRestantes) {
                continue;
            }

            // Crea una nueva combinación agregando la habitación actual
            $nuevaCombinacion = array_merge($combinacionActual, [$habitacion]);
            $nuevasPersonasRestantes = $personasRestantes - $capacidadHabitacion;

            // Se llama la función así misma para seguir buscando combinaciones (Llamada recursiva)
            $this->encontrarHabitaciones($habitaciones, $nuevasPersonasRestantes, $habitacionesSolicitadas, $nuevaCombinacion, $result);

            // Si ya se encontró una combinación válida, se detiene la búsqueda
            if ($result) {
                break;
            }
        }
    }
}
