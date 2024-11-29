<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservasServiciosSeeder extends Seeder
{
    public function run()
    {
        $reservas = DB::table('reservas')->select('reservaID', 'fechainicio', 'fechafin')->get();
        $servicios = DB::table('servicios')->get();

        foreach ($reservas as $reserva) {
            foreach ($servicios as $servicio) {
                $horario = $servicio->horario;

                // Verificar que la fecha del servicio esté dentro del rango de fechas de la reserva
                if ($horario >= $reserva->fechainicio . ' 00:00:00' && $horario <= $reserva->fechafin . ' 23:59:59') {
                    // Insertar en la tabla reservas_servicios
                    DB::table('reservas_servicios')->insert([
                        'servicioID' => $servicio->servicioID,
                        'reservaID' => $reserva->reservaID,
                    ]);
                }
            }
        }
    }
}