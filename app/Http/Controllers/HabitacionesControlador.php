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

class HabitacionesControlador extends Controller
{
    // Método que mostrará un gráfico sobre la disponibilidad de las habitaciones
    public function dispHabitaciones(Request $request)
    {
        // Obtener los datos de las habitaciones disponibles en cada hotel
        $data = $this->obtenerHabitacionesDisponibles();

        // Pasar los datos a la vista
        return view('disponibilidadhabitaciones', ['data' => $data]);
    }

    // Método para obtener las habitaciones disponibles de cada hotel
    private function obtenerHabitacionesDisponibles()
    {
        $fechaActual = Carbon::now()->format('Y-m-d');

        // Obtener todas las habitaciones por hotel junto con el nombre del hotel
        $habitacionesPorHotel = DB::table('hoteles')
            ->join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->leftJoin('reservas', function ($join) use ($fechaActual) {
                $join->on('habitaciones.habitacionID', '=', 'reservas.habitacionID')
                    ->where('reservas.fechainicio', '<=', $fechaActual)
                    ->where('reservas.fechafin', '>=', $fechaActual);
            })
            ->select('hoteles.nombre', DB::raw('COUNT(habitaciones.habitacionID) as total_habitaciones'), DB::raw('COUNT(reservas.reservaID) as habitaciones_reservadas'))
            ->groupBy('hoteles.nombre')
            ->get();

        // Procesar la disponibilidad de habitaciones por hotel
        $disponibilidadPorHotel = [];
        foreach ($habitacionesPorHotel as $hotel) {
            $disponibles = $hotel->total_habitaciones - $hotel->habitaciones_reservadas;
            $disponibilidadPorHotel[$hotel->nombre] = $disponibles; // Usa el nombre del hotel como clave
        }

        return $disponibilidadPorHotel;
    }
}
