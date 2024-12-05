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
    
    public function dispHabitaciones(Request $request)
    {
        
        $data = $this->obtenerHabitacionesDisponibles();
        $ingresos = $this->obtenerIngresosPorMes();
        $clientes = $this->obtenerClientesRegistradosPorMes();
        $servicios = $this->obtenerServiciosPorCategoria();
        $ingresosServicios = $this->obtenerIngresosServiciosPorMes();
        $numeroHoteles = DB::table('hoteles')->count();
        $numeroUsuarios = DB::table('users')->count();

        
        $totalIngresosReservas = array_sum(array_values($ingresos));
        $totalIngresosServicios = array_sum(array_values($ingresosServicios));
        $totalIngresosAnuales = $totalIngresosReservas + $totalIngresosServicios;

        $numeroReservasAnuales = DB::table('reservas')
        ->whereYear('fechainicio', Carbon::now()->year)
        ->count();

        
        return view('disponibilidadhabitaciones', [
            'data' => $data,
            'ingresos' => $ingresos,
            'clientes' => $clientes,
            'servicios' => $servicios,
            'ingresosServicios' => $ingresosServicios,
            'numeroHoteles' => $numeroHoteles,
            'totalIngresosAnuales' => $totalIngresosAnuales,
            'numeroReservasAnuales' => $numeroReservasAnuales,
            'numeroUsuarios' => $numeroUsuarios
        ]);
    }

    
    private function obtenerHabitacionesDisponibles()
    {
        $fechaActual = Carbon::now()->format('Y-m-d');

        
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

        
        $disponibilidadPorHotel = [];
        foreach ($habitacionesPorHotel as $hotel) {
            $disponibles = $hotel->total_habitaciones - $hotel->habitaciones_reservadas;
            $disponibilidadPorHotel[$hotel->nombre] = $disponibles; 
        }

        return $disponibilidadPorHotel;
    }

    
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

        
        $mesesDelAno = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = Carbon::now()->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $nombreMes = Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('F'); 
            $mesesDelAno[$nombreMes] = $ingresos[$mes] ?? 0;
        }

        return $mesesDelAno;
    }

    
    private function obtenerIngresosServiciosPorMes()
    {
        $ingresosServiciosPorMes = DB::table('servicios')
            ->select(DB::raw('DATE_FORMAT(horario, "%Y-%m") as mes'), DB::raw('SUM(precio) as ingresos'))
            ->whereYear('horario', Carbon::now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $ingresosServicios = [];
        foreach ($ingresosServiciosPorMes as $ingreso) {
            $ingresosServicios[$ingreso->mes] = $ingreso->ingresos;
        }

        
        $mesesDelAno = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = Carbon::now()->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $nombreMes = Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('F'); 
            $mesesDelAno[$nombreMes] = $ingresosServicios[$mes] ?? 0;
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

        
        $mesesDelAno = [];
        for ($i = 1; $i <= 12; $i++) {
            $mes = Carbon::now()->format('Y') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $nombreMes = Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('F'); 
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
