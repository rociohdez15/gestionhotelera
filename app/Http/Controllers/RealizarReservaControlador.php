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

        $reservaIDsArray = explode(',', $reservaIDs);

        // Obtener las reservas
        $reservas = Reserva::whereIn('reservaID', $reservaIDsArray)->get();
        if ($reservas->isEmpty()) {
            abort(404, 'Reservas no encontradas.');
        }

        // Obtener el cliente (se asume que todas las reservas son del mismo cliente)
        $cliente = Cliente::find($reservas->first()->clienteID);
        if (!$cliente) {
            abort(404, 'Cliente no encontrado.');
        }

        // Obtener las habitaciones reservadas
        $habitacionesReservadas = DB::table('reservas')
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->whereIn('reservas.reservaID', $reservaIDsArray)
            ->select('habitaciones.numhabitacion', 'habitaciones.precio', 'reservas.reservaID')
            ->get();

        // Obtener los servicios reservados (si aplica)
        $serviciosReservados = DB::table('reservas_servicios')
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID')
            ->whereIn('reservas_servicios.reservaID', $reservaIDsArray)
            ->get();

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

        // Tabla de cliente
        $html = '
        <h1 style="text-align: center;">Factura</h1>
        <h4>Datos del Cliente</h4>
        <table border="1" style="font-size: 10px; width: 100%; border-collapse: collapse;">
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>DNI</th>
                <th>Dirección</th>
            </tr>
            <tr>
                <td>' . $cliente->nombre . '</td>
                <td>' . $cliente->apellidos . '</td>
                <td>' . $cliente->email . '</td>
                <td>' . $cliente->telefono . '</td>
                <td>' . $cliente->dni . '</td>
                <td>' . $cliente->direccion . '</td>
            </tr>
        </table>';

        // Tabla de datos de la reserva
        $reserva = $reservas->first();
        $html .= '
        <h4>Detalles de la Reserva</h4>
        <table border="1" style="font-size: 10px; width: 100%; border-collapse: collapse;">
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>Fecha de Entrada</th>
                <th>Fecha de Salida</th>
                <th>Precio Total</th>
            </tr>
            <tr>
                <td>' . $reserva->fechainicio . '</td>
                <td>' . $reserva->fechafin . '</td>
                <td>' . number_format($reserva->preciototal, 2) . ' €</td>
            </tr>
        </table>';

        $html .= '
        <h4>Horario entrada-salida</h4>
        <table border="1" style="font-size: 10px; width: 100%; border-collapse: collapse;">
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>Hora de Check-in</th>
                <th>Hora de Check-out</th>
            </tr>
            <tr>
                <td>14:00h a 22:00h</td>
                <td>9:00h a 12:00h</td>
            </tr>
        </table>';

        // Tabla de habitaciones reservadas
        $html .= '
        <h4>Habitaciones Reservadas</h4>
        <table border="1" style="font-size: 10px; width: 100%; border-collapse: collapse;">
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>Reserva ID</th>
                <th>Número de Habitación</th>
                <th>Precio</th>
            </tr>';
        foreach ($habitacionesReservadas as $habitacion) {
            $html .= '
            <tr>
                <td>' . $habitacion->reservaID . '</td>
                <td>' . $habitacion->numhabitacion . '</td>
                <td>' . number_format($habitacion->precio, 2) . ' €</td>
            </tr>';
        }
        $html .= '</table>';



        // Tabla de servicios reservados
        if ($serviciosReservados->isNotEmpty()) {
            $html .= '
            <h2>Servicios Reservados</h2>
            <table border="1" style="font-size: 10px; width: 100%; border-collapse: collapse;">
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Horario</th>
                </tr>';
            foreach ($serviciosReservados as $servicio) {
                $html .= '
                <tr>
                    <td>' . $servicio->nombre . '</td>
                    <td>' . $servicio->descripcion . '</td>
                    <td>' . number_format($servicio->precio, 2) . ' €</td>
                    <td>' . $servicio->horario . '</td>
                </tr>';
            }
            $html .= '</table>';
        }

        // Escribir contenido HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Cerrar y generar el PDF
        $pdf->Output('factura.pdf', 'D');
    }
}
