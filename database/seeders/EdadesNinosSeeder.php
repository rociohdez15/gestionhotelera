<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdadesNinosSeeder extends Seeder
{
    public function run()
    {
        DB::table('edadesninos')->insert([
            ['edad' => 5, 'reservaID' => 1],
            ['edad' => 7, 'reservaID' => 2],
            ['edad' => 3, 'reservaID' => 5],
            ['edad' => 4, 'reservaID' => 5],
            ['edad' => 6, 'reservaID' => 7],
            ['edad' => 8, 'reservaID' => 7],
            ['edad' => 2, 'reservaID' => 9],
            ['edad' => 4, 'reservaID' => 9],
            ['edad' => 6, 'reservaID' => 9],
            ['edad' => 3, 'reservaID' => 12],
            ['edad' => 5, 'reservaID' => 15],
            ['edad' => 2, 'reservaID' => 17],
            ['edad' => 4, 'reservaID' => 17],
            ['edad' => 6, 'reservaID' => 17],
            ['edad' => 3, 'reservaID' => 19],
            ['edad' => 4, 'reservaID' => 21],
            ['edad' => 2, 'reservaID' => 22],
            ['edad' => 4, 'reservaID' => 22],
            ['edad' => 6, 'reservaID' => 22],
            ['edad' => 3, 'reservaID' => 28],
            ['edad' => 5, 'reservaID' => 28],
            ['edad' => 4, 'reservaID' => 29],
            ['edad' => 2, 'reservaID' => 36],
            ['edad' => 4, 'reservaID' => 36],
            ['edad' => 6, 'reservaID' => 36],
            ['edad' => 3, 'reservaID' => 37],
            ['edad' => 2, 'reservaID' => 40],
            ['edad' => 4, 'reservaID' => 40],
            ['edad' => 6, 'reservaID' => 40],
            ['edad' => 5, 'reservaID' => 41],
            ['edad' => 3, 'reservaID' => 49],
            ['edad' => 4, 'reservaID' => 50],
        ]);
    }
}
