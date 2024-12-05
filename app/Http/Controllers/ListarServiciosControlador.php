<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Servicio;
use App\Models\EdadNino;
use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\ReservaServicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use TCPDF;

class ListarServiciosControlador extends Controller
{
    public function listarServicios(Request $request)
    {

        $query = DB::table('reservas')
            ->join('habitaciones', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') // Unión con la tabla intermedia
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') // Unión con la tabla de servicios a través de la intermedia
            ->select(
                'reservas.*',
                'clientes.nombre',
                'clientes.apellidos',
                'habitaciones.numhabitacion',
                'servicios.servicioID',
                'hoteles.nombre as nombre_hotel',
                'servicios.nombre as nombre_servicio',
                DB::raw("SUBSTRING_INDEX(servicios.horario, ' ', 1) as dia_servicio"), // Separando el día del campo horario
                DB::raw("DATE_FORMAT(servicios.horario, '%H:%i') as hora_servicio") // Separando la hora del campo horario
            );

        $totalReservas = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalReservas / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $reservas = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'reservas' => $reservas,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarservicios', $parametros);
    }

    public function actualizarServicio()
    {
        $hoteles = DB::table('reservas')
            ->join('habitaciones', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') // Unión con la tabla intermedia
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') // Unión con la tabla de servicios a través de la intermedia
            ->select(
                'reservas.*',
                'clientes.nombre',
                'clientes.apellidos',
                'habitaciones.numhabitacion',
                'servicios.servicioID',
                'hoteles.nombre as nombre_hotel',
                'servicios.nombre as nombre_servicio',
                DB::raw("SUBSTRING_INDEX(servicios.horario, ' ', 1) as dia_servicio"), // Separando el día del campo horario
                DB::raw("DATE_FORMAT(servicios.horario, '%H:%i') as hora_servicio") // Separando la hora del campo horario
            )
            ->paginate(5);

        return response()->json($hoteles);
    }

    public function delServicio(Request $request, $servicioID)
    {
        $servicio = Servicio::find($servicioID);

        if (!$servicio) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'El servicio especificado no existe.'], 404);
            }
            return back()->withError('El servicio especificado no existe.');
        }

        // Eliminar el servicio de la tabla intermedia reservas_servicios
        DB::table('reservas_servicios')->where('servicioID', $servicioID)->delete();

        // Eliminar el servicio
        $servicio->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'El servicio se ha eliminado correctamente.',
                'servicio' => $servicio
            ]);
        }

        return redirect()->route('listarServicios')->with('status', 'El servicio se ha eliminado correctamente.');
    }


    public function mostrarServicio(Request $request, $servicioID)
    {
        // Obtener el servicio específico mediante el servicioID
        $servicio = DB::table('servicios')->where('servicioID', $servicioID)->first();
        if (!$servicio) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Servicio no encontrado'], 404);
            }
            return back()->withError('Servicio no encontrado');
        }

        // Separar la fecha y la hora del campo horario del servicio
        list($fecha, $hora) = explode(' ', $servicio->horario);

        // Obtener el hotel y las fechas de la reserva asociada al servicio
        $reserva = DB::table('hoteles')
            ->join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->join('reservas', 'habitaciones.habitacionID', '=', 'reservas.habitacionID')
            ->join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID')
            ->where('reservas_servicios.servicioID', $servicioID)
            ->select('hoteles.nombre as hotel_nombre', 'reservas.fechainicio', 'reservas.fechafin')
            ->first();

        if (!$reserva) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Reserva no encontrada'], 404);
            }
            return back()->withError('Reserva no encontrada');
        }

        // Obtener el cliente asociado a la reserva del servicio
        $cliente = DB::table('clientes')
            ->join('reservas', 'clientes.clienteID', '=', 'reservas.clienteID')
            ->join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID')
            ->where('reservas_servicios.servicioID', $servicioID)
            ->select('clientes.nombre', 'clientes.apellidos')
            ->first();

        if (!$cliente) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }
            return back()->withError('Cliente no encontrado');
        }

        $parametros = compact('servicio', 'fecha', 'hora', 'reserva', 'cliente');

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('editarservicio', $parametros);
    }

    public function editarServicio(Request $request, $servicioID)
    {
        // Encuentra el servicio por ID
        $servicio = Servicio::find($servicioID);
        if (!$servicio) {
            Log::error('Servicio no encontrado', ['servicioID' => $servicioID]);
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Servicio no encontrado'], 404);
            }
            return back()->withError('Servicio no encontrado');
        }

        try {
            // Valida que los campos 'fecha' y 'hora' estén presentes
            $validatedData = $request->validate([
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i',
            ]);

            // Combina la fecha y la hora para crear un solo campo 'horario'
            $fecha = Carbon::parse($validatedData['fecha']);
            $hora = Carbon::parse($validatedData['hora']);
            $horarioCompleto = $fecha->setTime($hora->hour, $hora->minute);

            // Actualiza el campo 'horario' del servicio
            $servicio->horario = $horarioCompleto;
            $servicio->save();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'El servicio se ha editado correctamente.',
                    'servicio' => $servicio
                ]);
            }

        
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al editar el servicio', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al editar el servicio')->withInput();
        }
    }
    public function generarPDF($servicioID)
    {
        // Consulta para generar el pdf de la lista de servicios filtrado por servicioID
        $reservas = Reserva::join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') // Relación con la tabla intermedia
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') // Relación con la tabla servicios
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select(
                'reservas.*',
                'hoteles.nombre as nombre_hotel',
                'habitaciones.numhabitacion',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellidos) as nombre_completo"),
                'servicios.servicioID', // ID del servicio
                'servicios.horario', // Campo horario de servicios
                'servicios.nombre as nombre_servicio', // Nombre del servicio
                DB::raw("DATE(servicios.horario) as fecha"), // Extraer la fecha del campo horario
                DB::raw("TIME(servicios.horario) as hora") // Extraer solo la parte de la hora
            )
            ->where('servicios.servicioID', $servicioID) // Filtrar por servicioID
            ->get();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de los datos
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Reserva de Servicio', 0, 1, 'C');

        // Crear el contenido en el PDF
        $html = '<div style="font-size: 12px; text-align: center;">';

        // Iterar sobre los datos de las reservas y agregarlos al contenido del PDF
        foreach ($reservas as $reserva) {
            $horario = $reserva->horario; // Obtiene el campo horario
            $fecha = date('Y-m-d', strtotime($horario)); // Extrae la fecha
            $hora = date('H:i', strtotime($horario)); // Extrae la hora

            $html .= '<div style="margin-bottom: 20px;">';
            $html .= '<strong>ID Servicio:</strong> ' . $reserva->servicioID . '<br>';
            $html .= '<strong>Nombre Cliente:</strong> ' . $reserva->nombre_completo . '<br>';
            $html .= '<strong>Nº Habitación:</strong> ' . $reserva->numhabitacion . '<br>';
            $html .= '<strong>Nombre Hotel:</strong> ' . $reserva->nombre_hotel . '<br>';
            $html .= '<strong>Nombre Servicio:</strong> ' . $reserva->nombre_servicio . '<br>';
            $html .= '<strong>Día:</strong> ' . $fecha . '<br>';
            $html .= '<strong>Hora:</strong> ' . $hora . '<br>';
            $html .= '</div>';
        }

        $html .= '</div>';

        // Agregar el contenido al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "reserva_servicio.pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function generarPDFTotal()
    {
        // Consulta para generar el pdf de la lista de servicios filtrado por servicioID
        $reservas = Reserva::join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') // Relación con la tabla intermedia
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') // Relación con la tabla servicios
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID')
            ->select(
                'reservas.*',
                'hoteles.nombre as nombre_hotel',
                'habitaciones.numhabitacion',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellidos) as nombre_completo"),
                'servicios.servicioID', // ID del servicio
                'servicios.horario', // Campo horario de servicios
                'servicios.nombre as nombre_servicio', // Nombre del servicio
                DB::raw("DATE(servicios.horario) as fecha"), // Extraer la fecha del campo horario
                DB::raw("TIME(servicios.horario) as hora") // Extraer solo la parte de la hora
            )
            ->get();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de la tabla
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Listado Servicios', 0, 1, 'C');

        // Crear la tabla en el PDF 
        $html = '<table border="1" style="font-size: 10px;">';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th>ID Servicio</th>';
        $html .= '<th>Nombre Cliente</th>';
        $html .= '<th>Nº Habitación</th>';
        $html .= '<th>Nombre Hotel</th>';
        $html .= '<th>Nombre Servicio</th>';
        $html .= '<th>Día</th>';
        $html .= '<th>Hora</th>';
        $html .= '</tr>';

        // Iterar sobre los datos de las reservas y agregarlos a la tabla del PDF
        foreach ($reservas as $reserva) {
            $horario = $reserva->horario; // Obtiene el campo horario
            $fecha = date('Y-m-d', strtotime($horario)); // Extrae la fecha
            $hora = date('H:i', strtotime($horario)); // Extrae la hora

            $html .= '<tr>';
            $html .= '<td>' . $reserva->servicioID . '</td>'; // ID del servicio
            $html .= '<td>' . $reserva->nombre_completo . '</td>'; // Nombre del cliente
            $html .= '<td>' . $reserva->numhabitacion . '</td>'; // Número de habitación
            $html .= '<td>' . $reserva->nombre_hotel . '</td>'; // Nombre del hotel
            $html .= '<td>' . $reserva->nombre_servicio . '</td>'; // Nombre del servicio
            $html .= '<td>' . $fecha . '</td>'; // Fecha extraída del campo horario
            $html .= '<td>' . $hora . '</td>'; // Hora extraída del campo horario
            $html .= '</tr>';
        }

        $html .= '</table>';

        // Agregar la tabla al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "lista_servicios.pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }


    public function buscarServicios(Request $request)
    {
        $query = $request->input('query');

        // Consulta para buscar servicios en el listado
        $consulta = Reserva::select(
            'reservas.*',
            'clientes.nombre',
            'clientes.apellidos',
            'habitaciones.numhabitacion',
            'hoteles.nombre as nombre_hotel',
            'servicios.servicioID',
            'servicios.nombre as nombre_servicio',
            DB::raw("SUBSTRING_INDEX(servicios.horario, ' ', 1) as dia_servicio"), // Separando el día del campo horario
            DB::raw("DATE_FORMAT(servicios.horario, '%H:%i') as hora_servicio")
        )
            ->join('clientes', 'reservas.clienteID', '=', 'clientes.clienteID') // Une con la tabla de clientes
            ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') // Une con la tabla de habitaciones
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID') // Une con la tabla de hoteles
            ->join('reservas_servicios', 'reservas.reservaID', '=', 'reservas_servicios.reservaID') // Une con la tabla intermedia
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID') // Une con la tabla de servicios
            ->where(function ($q) use ($query) {
                $q->where('reservas.reservaID', 'LIKE', "%$query%")
                    ->orWhere('clientes.nombre', 'LIKE', "%$query%")
                    ->orWhere('clientes.apellidos', 'LIKE', "%$query%")
                    ->orWhere('habitaciones.numhabitacion', 'LIKE', "%$query%")
                    ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
                    ->orWhere('servicios.nombre', 'LIKE', "%$query%")
                    ->orWhere('servicios.servicioID', 'LIKE', "%$query%");
            });

        $servicios = $consulta->orderBy('servicios.servicioID', 'asc')->paginate(5);

        if ($request->ajax()) {
            return response()->json($servicios);
        }

        return view('listarservicios', compact('servicios'));
    }

    public function anadirServicio(Request $request)
    {
        // Obtén todas las reservas desde la base de datos
        $reservas = Reserva::all();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['reservas' => $reservas]);
        }

        return view('anadirservicio', ['reservas' => $reservas]);
    }

    public function guardarServicio(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'reservaID' => 'required|exists:reservas,reservaID',
            'nombreServicio' => 'required|string',
            'fechaHora' => 'required|date_format:Y-m-d\TH:i',
        ]);

        // Obtener el número total de personas de la reserva
        $reservaID = $request->input('reservaID');
        $reserva = DB::table('reservas')->select('num_adultos', 'num_ninos')->where('reservaID', $reservaID)->first();

        if (!$reserva) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Reserva no encontrada'], 404);
            }
            return back()->withErrors(['error' => 'Reserva no encontrada'])->withInput();
        }

        // Verificar si el servicio ya existe para el mismo usuario, servicio, día y hora
        $servicioExistente = DB::table('reservas_servicios')
            ->join('servicios', 'reservas_servicios.servicioID', '=', 'servicios.servicioID')
            ->where('reservas_servicios.reservaID', $reservaID)
            ->where('servicios.nombre', $request->input('nombreServicio'))
            ->where('servicios.horario', $request->input('fechaHora'))
            ->first();

        if ($servicioExistente) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'El servicio ya existe para este usuario en la fecha y hora especificadas'], 409);
            }
            return back()->withErrors(['error' => 'El servicio ya existe para este usuario en la fecha y hora especificadas'])->withInput();
        }

        // Calcular el número total de personas
        $numPersonas = $reserva->num_adultos + $reserva->num_ninos;

        // Crear un nuevo servicio
        $servicio = new Servicio();
        $servicio->nombre = $request->input('nombreServicio');
        $servicio->descripcion = $request->input('nombreServicio');
        $servicio->horario = $request->input('fechaHora');
        // Calcular precio basado en el tipo de servicio y número de personas
        switch ($servicio->nombre) {
            case 'restaurante':
                $servicio->precio = 20 * $numPersonas;
                break;
            case 'spa':
                $servicio->precio = 25 * $numPersonas;
                break;
            case 'tour':
                $servicio->precio = 10 * $numPersonas;
                break;
            default:
                $servicio->precio = 0; // Precio por defecto si no se reconoce el servicio
                break;
        }
        $servicio->save();

        $servicioreserva = new ReservaServicio();
        $servicioreserva->reservaID = $request->input('reservaID');
        $servicioreserva->servicioID = $servicio->servicioID;
        $servicioreserva->save();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Servicio añadido correctamente.',
                'servicio' => $servicio,
                'servicioreserva' => $servicioreserva
            ]);
        }

        return redirect()->route('listarServicios')->with('status', 'Servicio añadido correctamente.');
    }
}
