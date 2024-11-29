<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResenasSeeder extends Seeder
{
    public function run()
    {
        $hoy = Carbon::now()->toDateString();
        $reservas = DB::table('reservas')
            ->where('fechafin', '<', $hoy)
            ->select('reservaID', 'clienteID', 'habitacionID', 'fechafin')
            ->get();

        $reservas = $reservas->random($reservas->count() / 2);

        $comentarios = [
            'Maravillosa estancia, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Excelente servicio y atención, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'La habitación estaba muy limpia y cómoda, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'El personal fue muy amable y servicial, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'La comida del restaurante fue deliciosa, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'El spa fue muy relajante, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'El tour fue muy informativo y divertido, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'La ubicación del hotel es perfecta, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'El precio es muy razonable para la calidad, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Definitivamente volvería a hospedarme aquí, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Tuve una experiencia maravillosa, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Todo estuvo perfecto, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Un lugar increíble para relajarse, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'El personal hizo que nuestra estancia fuera especial, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Las instalaciones son de primera, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Un ambiente muy acogedor, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'La vista desde la habitación era espectacular, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'El desayuno fue excelente, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Muy buena relación calidad-precio, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
            'Un hotel que recomendaría a todos, muy cercano al centro, buen restaurante, buenos servicios y buena limpieza.',
        ];

        foreach ($reservas as $reserva) {
            $cliente = DB::table('clientes')->where('clienteID', $reserva->clienteID)->first();
            $hotelID = DB::table('habitaciones')->where('habitacionID', $reserva->habitacionID)->value('hotelID');

            DB::table('resenas')->insert([
                'clienteID' => $reserva->clienteID,
                'hotelID' => $hotelID,
                'nombre_cliente' => $cliente->nombre,
                'texto' => $comentarios[array_rand($comentarios)],
                'puntuacion' => rand(1, 10),
                'fecha' => $reserva->fechafin,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}