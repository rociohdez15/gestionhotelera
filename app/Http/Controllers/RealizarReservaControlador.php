<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use App\Models\Reserva;
use App\Models\EdadNino;
use App\Models\Servicio;
use App\Models\Resena;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Cliente;
use Carbon\Carbon;
use TCPDF;

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
        $ubiacion = $request->input('ubicacion');

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
            "ubiacion" => $ubiacion,
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
                return redirect()->back()->withErrors([
                    'status' => 'Error: Ya existe una reserva para este cliente en esta habitación y fecha.',
                    'reservaExistente' => $reservaExistente
                ]); // Código de estado 400 para indicar error del cliente
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
                        'nombre' => 'tour',
                        'descripcion' => 'tour',
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

        return redirect()->route('pagoreserva', ['reservaIDs' => implode(',', array_column($reservas, 'reservaID'))]);
    }

    public function pagoreserva($reservaIDs)
    {
        $reservaIDsArray = explode(',', $reservaIDs);
        return view('pagoreserva', compact('reservaIDsArray'));
    }
    public function mostrarexito($reservaIDs)
    {
        $reservaIDsArray = explode(',', $reservaIDs);
        return view('exitoreserva', compact('reservaIDsArray'));
    }

    public function factura(Request $request, $reservaIDs)
    {
        if (!$reservaIDs) {
            abort(400, 'ReservaIDs son requeridos.');
        }

        // Convertir la cadena de IDs en un array
        $reservaIDsArray = explode(',', $reservaIDs);

        // Obtener las reservas
        $reservas = Reserva::whereIn('reservaID', $reservaIDsArray)->get();
        if ($reservas->isEmpty()) {
            abort(404, 'Reservas no encontradas.');
        }

        // Obtener el cliente (asumiendo que todas las reservas son del mismo cliente)
        $cliente = Cliente::find($reservas->first()->clienteID);
        if (!$cliente) {
            abort(404, 'Cliente no encontrado.');
        }

        // Obtener las habitaciones reservadas
        $habitacionesReservadas = DB::table('reservas')
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->whereIn('reservas.reservaID', $reservaIDsArray)
            ->select('habitaciones.numhabitacion', 'habitaciones.precio', 'reservas.reservaID', 'reservas.fechainicio', 'reservas.fechafin')
            ->get();

        if ($habitacionesReservadas->isEmpty()) {
            abort(404, 'No se encontraron habitaciones reservadas para estas reservas.');
        }

        // Cálculo del precio total considerando todas las habitaciones y las noches
        $precioTotalHabitaciones = 0;
        foreach ($habitacionesReservadas as $habitacion) {
            $fechaInicio = new \DateTime($habitacion->fechainicio);
            $fechaFin = new \DateTime($habitacion->fechafin);
            $noches = $fechaFin->diff($fechaInicio)->days;

            $precioTotalHabitaciones += $habitacion->precio * $noches;
        }

        // Obtener los servicios reservados
        $serviciosReservados = DB::table('reservas_servicios')
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID')
            ->whereIn('reservas_servicios.reservaID', $reservaIDsArray)
            ->select('servicios.nombre', 'servicios.precio')
            ->get();

        // Cálculo del precio total de los servicios
        $precioTotalServicios = 0;
        foreach ($serviciosReservados as $servicio) {
            $precioTotalServicios += $servicio->precio;
        }

        // Cálculo del total general (habitaciones + servicios)
        $subtotal = $precioTotalHabitaciones + $precioTotalServicios;

        // Calcular IVA (21%)
        $iva = $subtotal * 0.21;

        // Total final con IVA
        $totalConIVA = $subtotal + $iva;

        // Obtener los datos del hotel
        $hotel = DB::table('hoteles')->first();

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();

        // Configurar el documento PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('AlojaDirecto');
        $pdf->SetTitle('Factura');
        $pdf->SetSubject('Detalles de las Reservas');
        $pdf->SetKeywords('TCPDF, PDF, factura, cliente, reserva');

        // Configurar márgenes y cabeceras
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Añadir una página
        $pdf->AddPage();

        // Encabezado
        $pdf->SetFont('helvetica', 'B', 14);

        // Construcción del contenido HTML del PDF
        $html = '
    <style>
        table {
            font-size: 10px;
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center; /* Centrar texto */
        }
        th {
            background-color: #007BFF; /* Fondo azul */
            color: white; /* Texto en blanco */
            font-weight: bold;
        }
        h1, h4 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    <h1>Factura</h1>
    <h4>Datos del Hotel</h4>
    <table>
        <tr>
            <th style="background-color: #007BFF; color: white;">Nombre</th>
            <th style="background-color: #007BFF; color: white;">Dirección</th>
            <th style="background-color: #007BFF; color: white;">Ciudad</th>
            <th style="background-color: #007BFF; color: white;">Teléfono</th>
        </tr>
        <tr>
            <td>' . $hotel->nombre . '</td>
            <td>' . $hotel->direccion . '</td>
            <td>' . $hotel->ciudad . '</td>
            <td>' . $hotel->telefono . '</td>
        </tr>
    </table>
    <h4>Datos del Cliente</h4>
    <table>
        <tr>
            <th style="background-color: #007BFF; color: white;">Nombre</th>
            <th style="background-color: #007BFF; color: white;">Apellidos</th>
            <th style="background-color: #007BFF; color: white;">Email</th>
            <th style="background-color: #007BFF; color: white;">Teléfono</th>
            <th style="background-color: #007BFF; color: white;">DNI</th>
            <th style="background-color: #007BFF; color: white;">Dirección</th>
        </tr>
        <tr>
            <td>' . $cliente->nombre . '</td>
            <td>' . $cliente->apellidos . '</td>
            <td>' . $cliente->email . '</td>
            <td>' . $cliente->telefono . '</td>
            <td>' . $cliente->dni . '</td>
            <td>' . $cliente->direccion . '</td>
        </tr>
    </table>
    <h4>Detalles de las Reservas</h4>
    <table>
        <tr>
            <th style="background-color: #007BFF; color: white;">Reserva ID</th>
            <th style="background-color: #007BFF; color: white;">Número de Habitación</th>
            <th style="background-color: #007BFF; color: white;">Check-in</th>
            <th style="background-color: #007BFF; color: white;">Check-out</th>
            <th style="background-color: #007BFF; color: white;">Precio por Noche</th>
            <th style="background-color: #007BFF; color: white;">Número de Noches</th>
            <th style="background-color: #007BFF; color: white;">Subtotal</th>
        </tr>';

        foreach ($habitacionesReservadas as $habitacion) {
            $fechaInicio = new \DateTime($habitacion->fechainicio);
            $fechaFin = new \DateTime($habitacion->fechafin);
            $noches = $fechaFin->diff($fechaInicio)->days;

            $html .= '
        <tr>
            <td>' . $habitacion->reservaID . '</td>
            <td>' . $habitacion->numhabitacion . '</td>
            <td>' . $fechaInicio->format('d-m-Y') . '</td>
            <td>' . $fechaFin->format('d-m-Y') . '</td>
            <td>' . number_format($habitacion->precio, 2) . ' €</td>
            <td>' . $noches . '</td>
            <td>' . number_format($habitacion->precio * $noches, 2) . ' €</td>
        </tr>';
        }

        $html .= '
    </table>
    <h4>Horario entrada-salida</h4>
    <table>
        <tr>
            <th style="background-color: #007BFF; color: white;">Horario check-in</th>
            <th style="background-color: #007BFF; color: white;">Horario check-out</th>
        </tr>
        <tr>
            <td> 14:00h a 22:00h </td>
            <td> 09:00h a 12:00h </td>
        </tr>
    </table>
    <h4>Servicios Reservados</h4>
    <table style="font-size: 10px; width: 100%; border-collapse: collapse;">
        <tr style="background-color: #007BFF; color: white; font-weight: bold;">
            <th>Nombre del Servicio</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>';
        foreach ($serviciosReservados as $servicio) {
            $html .= '
        <tr>
            <td>' . $servicio->nombre . '</td>
            <td>' . number_format($servicio->precio, 2) . ' €</td>
            <td>' . number_format($servicio->precio, 2) . ' €</td>
        </tr>';
        }
        $html .= '</table>';

        $html .= '
    <h4>Resumen de la Factura</h4>
    <table>
        <tr>
            <td style="background-color: #007BFF; color: white;">Subtotal Habitaciones:</td>
            <td>' . number_format($precioTotalHabitaciones, 2) . ' €</td>
        </tr>
        <tr>
            <td style="background-color: #007BFF; color: white;">Subtotal Servicios:</td>
            <td>' . number_format($precioTotalServicios, 2) . ' €</td>
        </tr>
        <tr>
            <td style="background-color: #007BFF; color: white;">IVA (21%):</td>
            <td>' . number_format($iva, 2) . ' €</td>
        </tr>
        <tr>
            <td style="background-color: #007BFF; color: white;">Total:</td>
            <td style="background-color: green; color: white;">' . number_format($totalConIVA, 2) . ' €</td>
        </tr>
    </table>';

        // Añadir contenido al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Salida del PDF
        $pdf->Output('factura.pdf', 'I');
    }
}
