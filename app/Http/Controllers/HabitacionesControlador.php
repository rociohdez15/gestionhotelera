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
        $ingresos = $this->obtenerIngresosPorMes();
        $clientes = $this->obtenerClientesRegistradosPorMes();
        $servicios = $this->obtenerServiciosPorCategoria();

        // Pasar los datos a la vista
        return view('disponibilidadhabitaciones', [
            'data' => $data,
            'ingresos' => $ingresos,
            'clientes' => $clientes,
            'servicios' => $servicios
        ]);
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

    // Método para obtener los ingresos totales por meses
    private function obtenerIngresosPorMes()
    {
        $ingresosPorMes = DB::table('reservas')
            ->select(DB::raw('DATE_FORMAT(fechainicio, "%Y-%m") as mes'), DB::raw('SUM(preciototal) as ingresos'))
            ->whereYear('fechainicio', Carbon::now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $ingresos = [];
        foreach ($ingresosPorMes as $ingreso) {
            $ingresos[$ingreso->mes] = $ingreso->ingresos;
        }

        // Pasamos los meses del año al array de ingresos
        $mesesDelAno = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = Carbon::now()->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $nombreMes = Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('F'); // Obtener el nombre del mes en español
            $mesesDelAno[$nombreMes] = $ingresos[$mes] ?? 0;
        }

        return $mesesDelAno;
    }

    private function obtenerClientesRegistradosPorMes()
    {
        $clientesPorMes = DB::table('users')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', Carbon::now()->year)
            ->where('rolID', 3)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $clientes = [];
        foreach ($clientesPorMes as $cliente) {
            $clientes[$cliente->mes] = $cliente->total;
        }

        // Asegurarse de que todos los meses del año actual están presentes en el array
        $mesesDelAno = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = Carbon::now()->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $nombreMes = Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('F'); // Obtener el nombre del mes en español
            $mesesDelAno[$nombreMes] = $clientes[$mes] ?? 0;
        }

        return $mesesDelAno;
    }
    private function obtenerServiciosPorCategoria()
    {
        $serviciosPorCategoria = DB::table('servicios')
            ->select('nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('nombre')
            ->get();

        $servicios = [];
        foreach ($serviciosPorCategoria as $servicio) {
            $servicios[$servicio->nombre] = $servicio->total;
        }

        return $servicios;
    }
}
