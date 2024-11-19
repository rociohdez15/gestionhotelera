<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;

class BuscarHotelesControlador extends Controller
{
    //Método principal que mostrará los hoteles disponibles según el tipo de búsqueda realizada
    public function buscarhoteles(Request $request)
    {
        //Obtiene los parámetros de la búsqueda
        $ubicacion = $request->input('ubicacion');
        $fechaEntrada = $request->input('fecha_entrada');
        $fechaSalida = $request->input('fecha_salida');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $totalPersonas = $adultos + $ninos;
        $habitacionesSolicitadas = $request->input('habitaciones');
        

        $edadesNinos = []; // Array que almacenará las edades de los niños

        // Bucle que recorre el numero de niños
        for ($i = 1; $i <= $ninos; $i++) {
            // Obtiene la edad del niño 
            $edad = $request->input("edad-nino-$i");
            // Si la edad no es null la guarda en el array inicializado anteriormente
            if ($edad) {
                $edadesNinos[] = $edad;
            }
        }

        // Construir la cadena de parámetros para edades de niños
        $edadesNinosParams = ''; // Inicializa una cadena vacía para guardar los parámetros de las edades de los niños

        // El bucle recorre el array $edadesNinos, que contiene las edades de los niños
        foreach ($edadesNinos as $index => $edad) {

            /* Esto concatenará todas las edades de todos los niños introducidos en la búsqueda, teniendo
            en cuenta su $index + 1 irá numerando a cada niño (edad-nino-1, edad-nino-2, etc) y que al final
            se introducirá '&' para separar el siguiente parámetro */
            $edadesNinosParams .= 'edad-nino-' . ($index + 1) . '=' . $edad . '&';
        }
        $edadesNinosParams = rtrim($edadesNinosParams, '&'); // Elimina el último '&'

        // Se hace la consulta prinicipal para obtener los datos de los hoteles y su imagen de portada
        $hoteles = DB::table('hoteles') // Se selecciona la tabla hoteles como tabla principal de la consulta
            /* Realiza un left join con la tabla 'imagenes_hoteles' ya que devuelve todas las filas de
               la tabla de la izquierda, y las filas coincidentes de la tabla de la derecha. */
            ->leftJoin('imagenes_hoteles', function ($join) { 
                // Une la tabla de hoteles con la tabla de imágenes de los hoteles
                $join->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    // Filtra las imágenes para seleccionar sólo las que comienzan por 'portada'
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%') //En la BD se guardarán las imagenes de portadas de los hoteles con esa ruta para diferenciarlas
                    //Subconsulta para seleccionar solo una imagen por hotel para la portada
                    /* Selecciona el hotelID más pequeño de la tabla 'imagenes_hoteles' donde el 'hotelID' coincida 
                    con el hotel actual y solo selecciona las imágenes que son portadas*/
                    ->whereRaw('imagenes_hoteles.hotelID = (
                         SELECT MIN(hotelID) 
                         FROM imagenes_hoteles
                         WHERE hotelID = hoteles.hotelID
                           AND imagen LIKE "images/portadas/portada%"
                     )');
            })
            // Selecciona todos los campos de la tabla 'hoteles' y el campo 'imagen' de la tabla 'imagenes_hoteles' como 'imagen_url'
            ->select('hoteles.*', 'imagenes_hoteles.imagen as imagen_url');
            
            

        if (!empty($ubicacion)) { // Comprueba si el campo 'ubicación' no está vacío
            $hoteles->where(function ($filtroUbicacion) use ($ubicacion) { // Aplica un filtro a la consulta de hoteles usando una función 
                // Filtra hoteles por ciudad o nombre que contengan el campo ubicación
                $filtroUbicacion->where('hoteles.ciudad', 'like', '%' . $ubicacion . '%')
                    ->orWhere('hoteles.nombre', 'like', '%' . $ubicacion . '%');
            });
        }

        // Consulta que obtiene todos los hoteles y filtra aquellos que tienen habitaciones disponibles
        $hoteles = $hoteles->get()->filter(function ($hotel) use ($totalPersonas, $habitacionesSolicitadas, $fechaEntrada, $fechaSalida) { // Filtra a través de una función usando esos parámetros
            // Consulta para obtener habitaciones disponibles teniendo en cuenta las reservas hechas 
            $habitaciones = DB::table('habitaciones')
                /* Une la tabla de habitaciones con la tabla de reservas para obtener las habitaciones que
                tienen reservas en las fechas seleccionadas*/
                ->leftJoin('reservas', function ($consultaHabitaciones) use ($fechaEntrada, $fechaSalida) {
                    /* Une las tablas basándose en el ID de la habitación */
                    $consultaHabitaciones->on('habitaciones.habitacionID', '=', 'reservas.habitacionID')
                        ->where(function ($consultaFechas) use ($fechaEntrada, $fechaSalida) {
                            /* Filtra las reservas que coincidan con el rango de fechas solicitado,
                            resevas cuyo inicio y fin estás entre las fechas seleccionas*/
                            $consultaFechas->whereBetween('reservas.fechainicio', [$fechaEntrada, $fechaSalida])
                                ->orWhereBetween('reservas.fechafin', [$fechaEntrada, $fechaSalida])
                                ->orWhere(function ($consultaFechas2) use ($fechaEntrada, $fechaSalida) {
                                    // Consulta donde la reserva dura todo el rango de fechas solicitado
                                    $consultaFechas2->where('reservas.fechainicio', '<=', $fechaEntrada)
                                        ->where('reservas.fechafin', '>=', $fechaSalida);
                                });
                        });
                })
                //Filtra las habitaciones que no tienen reservas en el rango de fechas seleccionado
                ->whereNull('reservas.habitacionID')
                //Filtra las habitaciones que pertenecen al hotel actual
                ->where('habitaciones.hotelID', $hotel->hotelID)
                //Selecciona todas las columnas de la tabla 'habitaciones'
                ->select('habitaciones.*') 
                // Obtiene los resultados
                ->get();

                
            // Obtiene combinaciones de habitaciones que cumplen con el número de personas y habitaciones solicitadas
            $combinacionesValidas = $this->obtenerCombinacionesHabitaciones($habitaciones, $totalPersonas, $habitacionesSolicitadas);

            // Si se encuentran combinaciones válidas, asigna esas habitaciones al hotel actual
            if ($combinacionesValidas) {
                $hotel->habitaciones = $combinacionesValidas;
                return true; // Mantiene el hotel en los resultados si tiene habitaciones disponibles
            }

            // Descarta el hotel si no tiene combinaciones válidas de habitaciones disponibles
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

        // Obtiene los datos para la página actual
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
            "fechaEntrada" => $fechaEntrada,  
            "fechaSalida" => $fechaSalida,      
            'num_adultos' => $adultos,
            'num_ninos' => $ninos,
            '&' . $edadesNinosParams
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('buscarhoteles', $parametros); // Muestra la vista con los parametros anteriores
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

        // Si se excede el número de habitaciones solicitadas o si ya tenemos una combinación válida, se detiene la búsqueda
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

    public function reserve(Request $request)
    {
        // Obtiene los parámetros de la consulta
        $hotelID = $request->input('hotelID');
        $fecha_entrada = $request->input('fechaEntrada');
        $fecha_salida = $request->input('fechaSalida');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        // Obtiene el clienteID del usuario autenticado
        $clienteID = Auth::user()->id;
        $habitacionIDs = (array) $request->input('habitacionID');
        $precioHabitacion = $request->input('precioHabitacion');

        // Obtiene las edades de los niños
        $edadesNinos = [];
        for ($i = 1; $i <= $ninos; $i++) {
            $edad = $request->input("edad-nino-$i");
            if ($edad !== null) {
                $edadesNinos[] = $edad;
            }
        }

        // Supongamos que $edadesNinos es un array de edades de niños
        $edadesNinos = $request->input('edadesNinos', []);

        // Inicializar la cadena de parámetros para edades de niños
        $edadesNinosParams = '';

        // Construir la cadena de parámetros para edades de niños
        foreach ($edadesNinos as $index => $edad) {
            $edadesNinosParams .= 'edad-nino-' . ($index + 1) . '=' . urlencode($edad) . '&';
        }

        // Eliminar el último '&'
        $edadesNinosParams = rtrim($edadesNinosParams, '&');

        // Obtiene el hotel desde la base de datos
        $hotel = Hotel::find($hotelID);

        // Verifica si el usuario está autenticado
        if (Auth::check()) {
            // El usuario está autenticado, redirige a la página de realizar reservas
            return redirect()->to('/realizarreserva?hotelID=' . $hotelID . '&fechaEntrada=' . $fecha_entrada . '&fechaSalida=' . $fecha_salida . '&adultos=' . $adultos . '&ninos=' . $ninos . '&clienteID=' . $clienteID . '&' . $edadesNinosParams . '&habitacionID=' . implode(',', $habitacionIDs) .
                              '&precioHabitacion=' . $precioHabitacion );
        }

        // El usuario no está autenticado, guarda la URL actual y redirige al login
        $request->session()->put('url.intended', $request->input('redirect_url', url()->previous()));

        return redirect()->route('login'); // Redirige al login
    }
}
