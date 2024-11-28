<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['id' => 1, 'name' => 'Carlos', 'apellidos' => 'García Pérez', 'email' => 'carlos.garcia@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 2, 'name' => 'María', 'apellidos' => 'López Sánchez', 'email' => 'maria.lopez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 3, 'name' => 'Juan', 'apellidos' => 'Martínez Gómez', 'email' => 'juan.martinez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 4, 'name' => 'Ana', 'apellidos' => 'Hernández Díaz', 'email' => 'ana.hernandez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 5, 'name' => 'Luis', 'apellidos' => 'González Fernández', 'email' => 'luis.gonzalez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 6, 'name' => 'Laura', 'apellidos' => 'Rodríguez Ruiz', 'email' => 'laura.rodriguez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 7, 'name' => 'Pedro', 'apellidos' => 'López García', 'email' => 'pedro.lopez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 8, 'name' => 'Elena', 'apellidos' => 'Martín Sánchez', 'email' => 'elena.martin@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 9, 'name' => 'Javier', 'apellidos' => 'Jiménez Pérez', 'email' => 'javier.jimenez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 10, 'name' => 'Sara', 'apellidos' => 'García López', 'email' => 'sara.garcia@example.com', 'password' => Hash::make('1234567'), 'rolID' => 3],
            ['id' => 11, 'name' => 'Miguel', 'apellidos' => 'Fernández Gómez', 'email' => 'miguel.fernandez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 2],
            ['id' => 12, 'name' => 'Isabel', 'apellidos' => 'Ruiz Martínez', 'email' => 'isabel.ruiz@example.com', 'password' => Hash::make('1234567'), 'rolID' => 2],
            ['id' => 13, 'name' => 'Antonio', 'apellidos' => 'Díaz Hernández', 'email' => 'antonio.diaz@example.com', 'password' => Hash::make('1234567'), 'rolID' => 2],
            ['id' => 14, 'name' => 'Carmen', 'apellidos' => 'Pérez Rodríguez', 'email' => 'carmen.perez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 2],
            ['id' => 15, 'name' => 'Francisco', 'apellidos' => 'Gómez López', 'email' => 'francisco.gomez@example.com', 'password' => Hash::make('1234567'), 'rolID' => 2],
        ];

        DB::table('users')->insert($users);
    }
}
