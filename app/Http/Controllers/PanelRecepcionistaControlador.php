<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Reserva;
use Carbon\Carbon;


class PanelRecepcionistaControlador extends Controller
{
    // Método que mostrará el panel del recepcionista
    public function mostrarPanel(Request $request)
    {
        // Obtener los datos de los alojamientos mensuales
        $data = $this->obtenerAlojamientosMensuales();

        return view('panelrecepcionista', ['data' => $data]);
    }

    // Método para obtener los alojamientos mensuales
    private function obtenerAlojamientosMensuales()
{
    // Inicializa un array para los conteos por mes y día
    $alojamientosMensuales = array_fill(0, 12, array_fill(0, 31, 0)); 

    // Obtener las reservas agrupadas por mes y día
    $reservas = Reserva::selectRaw('MONTH(fechainicio) as mes, DAY(fechainicio) as dia, COUNT(*) as total')
        ->groupBy('mes', 'dia')
        ->orderBy('mes')
        ->orderBy('dia')
        ->get();

    // Rellenar el array con los datos obtenidos
    foreach ($reservas as $reserva) {
        $alojamientosMensuales[$reserva->mes - 1][$reserva->dia - 1] = $reserva->total;
    }

    return $alojamientosMensuales;
}
}
