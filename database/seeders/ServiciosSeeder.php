<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiciosSeeder extends Seeder
{
    public function run()
    {
        $servicios = [
            ['nombre' => 'restaurante', 'descripcion' => 'restaurante', 'precio' => 60],
            ['nombre' => 'tour', 'descripcion' => 'tour', 'precio' => 40],
            ['nombre' => 'spa', 'descripcion' => 'spa', 'precio' => 50],
        ];

        $reservas = DB::table('reservas')->select('reservaID', 'fechainicio')->get();
        $reservas = $reservas->random(25);

        foreach ($reservas as $reserva) {
            $servicio = $servicios[array_rand($servicios)];
            $horario = $reserva->fechainicio . ' 09:00:00'; // Concatenar la fecha de inicio con la hora

            DB::table('servicios')->insert([
                'nombre' => $servicio['nombre'],
                'descripcion' => $servicio['descripcion'],
                'precio' => $servicio['precio'],
                'horario' => $horario,
            ]);
        }
    }
}
