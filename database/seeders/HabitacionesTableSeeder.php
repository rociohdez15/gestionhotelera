<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitacionesTableSeeder extends Seeder
{
    public function run()
    {
        $habitaciones = [];
        $habitacionID = 1;
    
        $precios = [
            1 => [50, 100],  
            2 => [101, 150], 
            3 => [151, 200], 
            4 => [201, 300] 
        ];
    
        for ($hotelID = 1; $hotelID <= 15; $hotelID++) {
            for ($i = 1; $i <= 4; $i++) {
                $precio = rand($precios[$i][0], $precios[$i][1]); 
                $habitaciones[] = [
                    'habitacionID' => $habitacionID,
                    'numhabitacion' => 100 + $i,
                    'tipohabitacion' => $i,
                    'precio' => $precio,
                    'hotelID' => $hotelID,
                ];
                $habitacionID++;
            }
        }
    
        DB::table('habitaciones')->insert($habitaciones);
    }
}
