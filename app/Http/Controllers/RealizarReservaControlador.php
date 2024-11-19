<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use App\Models\Reserva;
use App\Models\EdadNino;
use App\Models\Servicio;
use App\Models\Resena;
use Carbon\Carbon;

class RealizarReservaControlador extends Controller
{
    /* Método que muestra la vista y obtiene los datos necesarios para la realización de la reserva. */
    public function realizarreserva(Request $request)
    {
        // Obtén los parámetros de la consulta
        $hotelID = $request->input('hotelID');
        $fechaEntrada = $request->input('fechaEntrada');
        $fechaSalida = $request->input('fechaSalida');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $clienteID = $request->input('clienteID');
        $precioHabitacion = $request->input('precioHabitacion');

        // Convierte el habitacionID a un array si no lo es
        $habitacionID = $request->input('habitacionID');
        if (is_string($habitacionID)) {
            $habitacionID = explode(',', $habitacionID);
        }

        $edadesNinos = []; // Array que almacenará las edades de los niños

        // Bucle que recorre el numero de niños
        for ($i = 1; $i <= $ninos; $i++) {
            // Obtiene la edad del niño 
            $edad = $request->input("edad-nino-$i");

            // Si la edad no es null la guarda en el array inicializado anteriormente
            if ($edad !== null) {
                $edadesNinos[] = $edad;
            }
        }

        // Obtiene los datos del hotel usando el ID 
        $hotel = Hotel::findOrFail($hotelID);

        // Obtiene las imágenes del hotel que empiecen por esa ruta y esten almacenadas en la columna imagen
        $imagenes = DB::table('imagenes_hoteles')
            ->where('hotelID', $hotelID)
            ->where('imagen', 'like', 'images/hoteles/%')
            ->pluck('imagen');

        // Obtiene las reseñas del hotel
        $resenas = Resena::where('hotelID', $hotelID)->get();

        $parametros = [
            "tituloventana" => "AlojaDirecto | Realizar Reserva",
            "hotel" => $hotel,
            "imagenes" => $imagenes,
            "fechaEntrada" => $fechaEntrada,
            "fechaSalida" => $fechaSalida,
            "adultos" => $adultos,
            "ninos" => $ninos,
            "clienteID" => $clienteID,
            "edadesNinos" => $edadesNinos,
            "habitacionID" => $habitacionID,
            "precioHabitacion" => $precioHabitacion,
            "resenas" => $resenas
        ];

        // Verificar si la solicitud es para JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('realizarreserva', $parametros);
    }


    /* Método para crear la reserva y a la misma vez almacenar las edades de los niños y los servicios 
    adicionales de la misma */
    public function guardarreserva(Request $request)
    {
        // Obtiene los parámetros
        $fechaEntrada = $request->input('fechaEntrada');
        $fechaSalida = $request->input('fechaSalida');
        $clienteID = $request->input('clienteID');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $precioHabitacion = $request->input('precioHabitacion');
        $habitacionID = $request->input('habitacionID');
        $estado = 'Pendiente';
        $edadesNinos = $request->input('edadesNinos', []);

        // Obtiene las fechas introducidas de los servicios adicionales (Opcional)
        $fechaRestaurante = $request->input('fecha-restaurante');
        $fechaSpa = $request->input('fecha-spa');
        $fechaTours = $request->input('fecha-tours');

        // Verifica si habitacionID es un array
        if (!is_array($habitacionID)) {
            $habitacionID = explode(',', $habitacionID);
        }

        // Calcula el número de noches calculando la diferencia de dias entre dos fechas 
        $fechaEntradaCarbon = Carbon::parse($fechaEntrada);
        $fechaSalidaCarbon = Carbon::parse($fechaSalida);
        $numNoches = $fechaEntradaCarbon->diffInDays($fechaSalidaCarbon);

        // Calcula el precio total
        $precioTotal = $precioHabitacion * $numNoches;

        $reservas = [];
        $serviciosReservados = [];

        // Hacemos la inserción en la tabla reservas
        foreach ($habitacionID as $id) {

            // Comprueba si ya existe una reserva para esta combinación
            $reservaExistente = Reserva::where('fechainicio', $fechaEntrada)
                ->where('fechafin', $fechaSalida)
                ->where('clienteID', $clienteID)
                ->where('habitacionID', $id)
                ->first();

            if ($reservaExistente) {
                return response()->json([
                    'status' => 'Error: Ya existe una reserva para este cliente en esta habitación y fecha.',
                    'reservaExistente' => $reservaExistente
                ], 400); // Código de estado 400 para indicar error del cliente
            } else {

                $reserva = Reserva::create([
                    'fechainicio' => $fechaEntrada,
                    'fechafin' => $fechaSalida,
                    'estado' => $estado,
                    'preciototal' => $precioTotal,
                    'num_adultos' => $adultos,
                    'num_ninos' => $ninos,
                    'fecha_checkin' => $fechaEntrada,
                    'fecha_checkout' => $fechaSalida,
                    'clienteID' => $clienteID,
                    'habitacionID' => $id,
                ]);

                $reservas[] = $reserva;

                // Si hay niños, se guarda las edades en la tabla edadesninos
                if (!empty($ninos) && !empty($edadesNinos)) {
                    foreach ($edadesNinos as $edad) {
                        // Solo guarda si la edad es válida (Mayor o igual a 0 y menor o igual a 17)
                        if (is_numeric($edad) && $edad > 0) {
                            EdadNino::create([
                                'edad' => $edad,
                                'reservaID' => $reserva->reservaID,
                            ]);
                        }
                    }
                }

                // Inserta servicios adicionales si las fechas son introducidas
                $numPersonas = $adultos + $ninos;
                $servicios = [];

                //Si se ha introducido fecha de restaurante, se inserta el servicio de restaurante
                if (!empty($fechaRestaurante)) {
                    $servicios[] = [
                        'nombre' => 'restaurante',
                        'descripcion' => 'restaurante',
                        'precio' => 20 * $numPersonas,
                        'horario' => $fechaRestaurante,
                    ];
                }

                //Si se ha introducido fecha de spa, se inserta el servicio de spa
                if (!empty($fechaSpa)) {
                    $servicios[] = [
                        'nombre' => 'spa',
                        'descripcion' => 'spa',
                        'precio' => 25 * $numPersonas,
                        'horario' => $fechaSpa,
                    ];
                }

                //Si se ha introducido fecha de tours, se inserta el servicio de tours
                if (!empty($fechaTours)) {
                    $servicios[] = [
                        'nombre' => 'tours',
                        'descripcion' => 'tours',
                        'precio' => 10 * $numPersonas,
                        'horario' => $fechaTours,
                    ];
                }

                // Inserta los servicios y las relaciones en la tabla intermedia
                foreach ($servicios as $servicioData) {
                    $servicio = Servicio::create($servicioData);
                    $serviciosReservados[] = $servicio;

                    // Relaciona el servicio con la reserva en la tabla intermedia
                    DB::table('reservas_servicios')->insert([
                        'reservaID' => $reserva->reservaID,
                        'servicioID' => $servicio->servicioID,
                    ]);
                }
            }
        }
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => '¡Reserva realizada correctamente!',
                'reservas' => $reservas,
                'servicios' => $serviciosReservados
            ]);
        }

        return redirect()->route('exitoreserva');
    }

    public function mostrarexito()
    {
        return view('exitoreserva');
    }
}
