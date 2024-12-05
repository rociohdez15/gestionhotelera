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
    private $provincias = [
        'Álava' => ['Vitoria-Gasteiz', 'Llodio', 'Amurrio'],
        'Albacete' => ['Albacete', 'Hellín', 'Villarrobledo'],
        'Alicante' => ['Alicante', 'Elche', 'Torrevieja'],
        'Almería' => ['Almería', 'Roquetas de Mar', 'El Ejido'],
        'Asturias' => ['Oviedo', 'Gijón', 'Avilés'],
        'Ávila' => ['Ávila', 'Arévalo', 'Arenas de San Pedro'],
        'Badajoz' => ['Badajoz', 'Mérida', 'Don Benito'],
        'Baleares' => ['Palma', 'Ibiza', 'Manacor'],
        'Barcelona' => ['Barcelona', 'Hospitalet de Llobregat', 'Badalona'],
        'Burgos' => ['Burgos', 'Miranda de Ebro', 'Aranda de Duero'],
        'Cáceres' => ['Cáceres', 'Plasencia', 'Navalmoral de la Mata'],
        'Cádiz' => ['Cádiz', 'Jerez de la Frontera', 'Algeciras'],
        'Cantabria' => ['Santander', 'Torrelavega', 'Castro Urdiales'],
        'Castellón' => ['Castellón de la Plana', 'Villarreal', 'Burriana'],
        'Ciudad Real' => ['Ciudad Real', 'Puertollano', 'Tomelloso'],
        'Córdoba' => ['Córdoba', 'Lucena', 'Puente Genil'],
        'Cuenca' => ['Cuenca', 'Tarancón', 'San Clemente'],
        'Girona' => ['Girona', 'Figueres', 'Blanes'],
        'Granada' => ['Granada', 'Motril', 'Almuñécar'],
        'Guadalajara' => ['Guadalajara', 'Azuqueca de Henares', 'Alovera'],
        'Guipúzcoa' => ['San Sebastián', 'Irún', 'Eibar'],
        'Huelva' => ['Huelva', 'Lepe', 'Almonte'],
        'Huesca' => ['Huesca', 'Monzón', 'Barbastro'],
        'Jaén' => ['Jaén', 'Linares', 'Andújar'],
        'La Rioja' => ['Logroño', 'Calahorra', 'Arnedo'],
        'Las Palmas' => ['Las Palmas de Gran Canaria', 'Telde', 'Santa Lucía de Tirajana'],
        'León' => ['León', 'Ponferrada', 'San Andrés del Rabanedo'],
        'Lleida' => ['Lleida', 'Balaguer', 'Tàrrega'],
        'Lugo' => ['Lugo', 'Monforte de Lemos', 'Viveiro'],
        'Madrid' => ['Madrid', 'Móstoles', 'Alcalá de Henares'],
        'Málaga' => ['Málaga', 'Marbella', 'Mijas'],
        'Murcia' => ['Murcia', 'Cartagena', 'Lorca'],
        'Navarra' => ['Pamplona', 'Tudela', 'Barañáin'],
        'Ourense' => ['Ourense', 'Verín', 'O Barco de Valdeorras'],
        'Palencia' => ['Palencia', 'Guardo', 'Aguilar de Campoo'],
        'Pontevedra' => ['Vigo', 'Pontevedra', 'Vilagarcía de Arousa'],
        'Salamanca' => ['Salamanca', 'Béjar', 'Ciudad Rodrigo'],
        'Santa Cruz de Tenerife' => ['Santa Cruz de Tenerife', 'San Cristóbal de La Laguna', 'Arona'],
        'Segovia' => ['Segovia', 'Cuéllar', 'San Ildefonso'],
        'Sevilla' => ['Sevilla', 'Dos Hermanas', 'Alcalá de Guadaíra'],
        'Soria' => ['Soria', 'Almazán', 'Ólvega'],
        'Tarragona' => ['Tarragona', 'Reus', 'Vendrell'],
        'Teruel' => ['Teruel', 'Alcañiz', 'Andorra'],
        'Toledo' => ['Toledo', 'Talavera de la Reina', 'Illescas'],
        'Valencia' => ['Valencia', 'Gandía', 'Torrent'],
        'Valladolid' => ['Valladolid', 'Medina del Campo', 'Laguna de Duero'],
        'Vizcaya' => ['Bilbao', 'Barakaldo', 'Getxo'],
        'Zamora' => ['Zamora', 'Benavente', 'Toro'],
        'Zaragoza' => ['Zaragoza', 'Calatayud', 'Utebo'],
    ];


    public function getMunicipios($provincia)
    {
        if (array_key_exists($provincia, $this->provincias)) {
            return response()->json($this->provincias[$provincia]);
        } else {
            return response()->json([]);
        }
    }

    
    public function realizarreserva(Request $request)
    {
        
        $hotelID = $request->input('hotelID');
        $fechaEntrada = $request->input('fechaEntrada');
        $fechaSalida = $request->input('fechaSalida');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $clienteID = $request->input('clienteID');
        $precioHabitacion = $request->input('precioHabitacion');
        $ubiacion = $request->input('ubicacion');

        
        $habitacionID = $request->input('habitacionID');
        if (is_string($habitacionID)) {
            $habitacionID = explode(',', $habitacionID);
        }

        $edadesNinos = []; 

        
        for ($i = 1; $i <= $ninos; $i++) {
            
            $edad = $request->input("edad-nino-$i");

            
            if ($edad !== null) {
                $edadesNinos[] = $edad;
            }
        }

        
        $hotel = Hotel::findOrFail($hotelID);

        
        $imagenes = DB::table('imagenes_hoteles')
            ->where('hotelID', $hotelID)
            ->where('imagen', 'like', 'images/hoteles/%')
            ->pluck('imagen');

        
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

        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('realizarreserva', $parametros);
    }


    /* Método para crear la reserva y a la misma vez almacenar las edades de los niños y los servicios 
    adicionales de la misma */
    public function guardarreserva(Request $request)
    {
        
        $fechaEntrada = $request->input('fechaEntrada');
        $fechaSalida = $request->input('fechaSalida');
        $clienteID = $request->input('clienteID');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $precioHabitacion = $request->input('precioHabitacion');
        $habitacionID = $request->input('habitacionID');
        $estado = 'Pendiente';
        $edadesNinos = $request->input('edadesNinos', []);

        
        $fechaRestaurante = $request->input('fecha-restaurante');
        $fechaSpa = $request->input('fecha-spa');
        $fechaTours = $request->input('fecha-tours');

        
        if (!is_array($habitacionID)) {
            $habitacionID = explode(',', $habitacionID);
        }

        
        $fechaEntradaCarbon = Carbon::parse($fechaEntrada);
        $fechaSalidaCarbon = Carbon::parse($fechaSalida);
        $numNoches = $fechaEntradaCarbon->diffInDays($fechaSalidaCarbon);

        
        $precioTotal = $precioHabitacion * $numNoches;

        $reservas = [];
        $serviciosReservados = [];

        
        foreach ($habitacionID as $id) {

            
            $reservaExistente = Reserva::where('fechainicio', $fechaEntrada)
                ->where('fechafin', $fechaSalida)
                ->where('clienteID', $clienteID)
                ->where('habitacionID', $id)
                ->first();

            if ($reservaExistente) {
                return redirect()->back()->withErrors([
                    'status' => 'Error: Ya existe una reserva para este cliente en esta habitación y fecha.',
                    'reservaExistente' => $reservaExistente
                ]); 
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

                
                if (!empty($ninos) && !empty($edadesNinos)) {
                    foreach ($edadesNinos as $edad) {
                        
                        if (is_numeric($edad) && $edad > 0) {
                            EdadNino::create([
                                'edad' => $edad,
                                'reservaID' => $reserva->reservaID,
                            ]);
                        }
                    }
                }

                
                $numPersonas = $adultos + $ninos;
                $servicios = [];

                
                if (!empty($fechaRestaurante)) {
                    $servicios[] = [
                        'nombre' => 'restaurante',
                        'descripcion' => 'restaurante',
                        'precio' => 20 * $numPersonas,
                        'horario' => $fechaRestaurante,
                    ];
                }

                
                if (!empty($fechaSpa)) {
                    $servicios[] = [
                        'nombre' => 'spa',
                        'descripcion' => 'spa',
                        'precio' => 25 * $numPersonas,
                        'horario' => $fechaSpa,
                    ];
                }

                
                if (!empty($fechaTours)) {
                    $servicios[] = [
                        'nombre' => 'tour',
                        'descripcion' => 'tour',
                        'precio' => 10 * $numPersonas,
                        'horario' => $fechaTours,
                    ];
                }

                
                foreach ($servicios as $servicioData) {
                    $servicio = Servicio::create($servicioData);
                    $serviciosReservados[] = $servicio;

                    
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
        $provincias = array_keys($this->provincias);
        return view('pagoreserva', compact('reservaIDsArray', 'provincias'));
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

        
        $reservas = Reserva::whereIn('reservaID', $reservaIDsArray)->get();
        if ($reservas->isEmpty()) {
            abort(404, 'Reservas no encontradas.');
        }

        
        $cliente = Cliente::find($reservas->first()->clienteID);
        if (!$cliente) {
            abort(404, 'Cliente no encontrado.');
        }

        
        $habitacionesReservadas = DB::table('reservas')
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->whereIn('reservas.reservaID', $reservaIDsArray)
            ->select('habitaciones.numhabitacion', 'habitaciones.precio', 'reservas.reservaID', 'reservas.fechainicio', 'reservas.fechafin')
            ->get();

        if ($habitacionesReservadas->isEmpty()) {
            abort(404, 'No se encontraron habitaciones reservadas para estas reservas.');
        }

        
        $precioTotalHabitaciones = 0;
        foreach ($habitacionesReservadas as $habitacion) {
            $fechaInicio = new \DateTime($habitacion->fechainicio);
            $fechaFin = new \DateTime($habitacion->fechafin);
            $noches = $fechaFin->diff($fechaInicio)->days;

            $precioTotalHabitaciones += $habitacion->precio * $noches;
        }

        
        $serviciosReservados = DB::table('reservas_servicios')
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID')
            ->whereIn('reservas_servicios.reservaID', $reservaIDsArray)
            ->select('servicios.nombre', 'servicios.precio')
            ->get();

        
        $precioTotalServicios = 0;
        foreach ($serviciosReservados as $servicio) {
            $precioTotalServicios += $servicio->precio;
        }

        
        $subtotal = $precioTotalHabitaciones + $precioTotalServicios;

        
        $iva = $subtotal * 0.21;

        
        $totalConIVA = $subtotal + $iva;

        
        $hotel = DB::table('hoteles')->first();

        
        $pdf = new TCPDF();

        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('AlojaDirecto');
        $pdf->SetTitle('Factura');
        $pdf->SetSubject('Detalles de las Reservas');
        $pdf->SetKeywords('TCPDF, PDF, factura, cliente, reserva');

        
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        
        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 14);

        
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
            text-align: center; 
        }
        th {
            background-color: #007BFF; 
            color: white; 
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

        
        $pdf->writeHTML($html, true, false, true, false, '');

        
        $pdf->Output('factura.pdf', 'I');
    }



}

