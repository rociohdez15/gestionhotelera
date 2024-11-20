<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\EdadNino;
use App\Models\Reserva;
use App\Models\Habitacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use TCPDF;

class ListarHabitacionesControlador extends Controller
{
    public function listarHabitaciones(Request $request)
    {
        $query = DB::table('habitaciones')
            ->select('habitacionID', 'numhabitacion', 'tipohabitacion', 'precio', 'hotelID');

        $totalHabitaciones = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHabitaciones / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $habitaciones = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'habitaciones' => $habitaciones,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarhabitaciones', $parametros);
    }

    public function delHabitacion($habitacionID, Request $request)
    {
        // Buscar la habitación
        $habitacion = DB::table('habitaciones')->where('habitacionID', $habitacionID)->first();

        if (!$habitacion) {
            return back()->withError('La habitación especificada no existe.');
        }

        // Eliminar la habitación
        DB::table('habitaciones')->where('habitacionID', $habitacionID)->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'La habitación se ha eliminado correctamente.',
                'habitacion' => $habitacion
            ]);
        }

        return redirect()->route('listarHabitaciones')->with('status', 'La habitación se ha eliminado correctamente.');
    }

    public function generarPDF($habitacionID)
    {
        // Consulta para obtener los datos de la habitación específica
        $habitacion = DB::table('habitaciones')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->select(
                'habitaciones.*',
                'hoteles.nombre as nombre_hotel'
            )
            ->where('habitaciones.habitacionID', $habitacionID)
            ->first();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de los datos
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos de la Habitación', 0, 1, 'C');

        // Crear el contenido en el PDF
        $html = '<div style="font-size: 12px; text-align: center;">';

        // Agregar los datos de la habitación al contenido del PDF
        if ($habitacion) {
            $html .= '<div style="margin-bottom: 20px;">';
            $html .= '<strong>ID Habitación:</strong> ' . $habitacion->habitacionID . '<br>';
            $html .= '<strong>Número de Habitación:</strong> ' . $habitacion->numhabitacion . '<br>';
            $html .= '<strong>Tipo de Habitación:</strong> ' . $habitacion->tipohabitacion . '<br>';
            $html .= '<strong>Precio:</strong> ' . $habitacion->precio . '<br>';
            $html .= '<strong>Hotel:</strong> ' . $habitacion->nombre_hotel . '<br>';
            $html .= '</div>';
        } else {
            $html .= '<div>No se encontraron datos para la habitación especificada.</div>';
        }

        $html .= '</div>';

        // Agregar el contenido al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "habitacion_" . $habitacionID . ".pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function generarPDFTotal()
    {
        // Consulta para generar un pdf del listado de habitaciones
        $habitaciones = DB::table('habitaciones')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->select(
                'habitaciones.*',
                'hoteles.nombre as nombre_hotel'
            )
            ->get();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de la tabla
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Listado de Habitaciones', 0, 1, 'C');

        // Crear la tabla en el PDF 
        $html = '<table border="1" style="font-size: 10px;">';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th>ID Habitación</th>';
        $html .= '<th>Número de Habitación</th>';
        $html .= '<th>Tipo de Habitación</th>';
        $html .= '<th>Precio</th>';
        $html .= '<th>Hotel</th>';
        $html .= '</tr>';

        // Iterar sobre los datos de las habitaciones y agregarlos a la tabla del PDF
        foreach ($habitaciones as $habitacion) {
            $html .= '<tr>';
            $html .= '<td>' . $habitacion->habitacionID . '</td>'; // ID de la habitación
            $html .= '<td>' . $habitacion->numhabitacion . '</td>'; // Número de habitación
            $html .= '<td>' . $habitacion->tipohabitacion . '</td>'; // Tipo de habitación
            $html .= '<td>' . $habitacion->precio . '</td>'; // Precio
            $html .= '<td>' . $habitacion->nombre_hotel . '</td>'; // Nombre del hotel
            $html .= '</tr>';
        }

        $html .= '</table>';

        // Agregar la tabla al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "lista_habitaciones.pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function buscarHabitaciones(Request $request)
    {
        $query = $request->input('query');

        // Consulta para utilizar el buscador en el listado de habitaciones
        $consulta = DB::table('habitaciones')
            ->join('hoteles', 'habitaciones.hotelID', '=', 'hoteles.hotelID')
            ->select('habitaciones.*', 'hoteles.nombre as nombre_hotel')
            ->where('habitaciones.habitacionID', 'LIKE', "%$query%")
            ->orWhere('habitaciones.numhabitacion', 'LIKE', "%$query%")
            ->orWhere('habitaciones.tipohabitacion', 'LIKE', "%$query%")
            ->orWhere('habitaciones.precio', 'LIKE', "%$query%")
            ->orWhere('hoteles.nombre', 'LIKE', "%$query%");

        $totalHabitaciones = $consulta->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHabitaciones / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $habitaciones = $consulta->skip($inicio)->take($registros_por_pagina)->get();

        return view('listarhabitaciones', [
            'habitaciones' => $habitaciones,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ], compact('habitaciones'));
    }
}
