<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservasTableSeeder extends Seeder
{

    public function run()
    {
        $reservas = [];
        $reservaID = 1;

        // Obtener todas las habitaciones
        $habitaciones = DB::table('habitaciones')->get();

        for ($i = 1; $i <= 50; $i++) {
            $fechainicio = Carbon::createFromTimestamp(rand(strtotime('2024-01-01'), strtotime('2024-12-31')));
            $fechafin = $fechainicio->copy()->addDays(rand(2, 5)); // Reservas de entre 2 y 5 días

            $num_adultos = rand(1, 4);
            $num_ninos = rand(0, 4 - $num_adultos);

            // Filtrar habitaciones adecuadas para el número de adultos y niños
            $habitacion = $habitaciones->filter(function ($habitacion) use ($num_adultos, $num_ninos) {
                return $habitacion->tipohabitacion >= ($num_adultos + $num_ninos);
            })->random();

            $precioHabitacion = $habitacion->precio;
            $noches = $fechainicio->diffInDays($fechafin);
            $preciototal = $precioHabitacion * $noches;

            $reservas[] = [
                'reservaID' => $reservaID,
                'fechainicio' => $fechainicio->format('Y-m-d'),
                'fechafin' => $fechafin->format('Y-m-d'),
                'estado' => '',
                'preciototal' => $preciototal,
                'num_adultos' => $num_adultos,
                'num_ninos' => $num_ninos,
                'fecha_checkin' => $fechainicio->format('Y-m-d 00:00:00'),
                'fecha_checkout' => $fechafin->format('Y-m-d 00:00:00'),
                'clienteID' => rand(1, 10),
                'habitacionID' => $habitacion->habitacionID,
            ];

            $reservaID++;
        }

        DB::table('reservas')->insert($reservas);
    }
}
