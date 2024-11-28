<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientesTableSeeder extends Seeder
{
    public function run()
    {
        $clientes = [
            ['clienteID' => 1, 'nombre' => 'Carlos', 'apellidos' => 'García Pérez', 'direccion' => 'Calle Falsa 123', 'telefono' => '600123456', 'dni' => '12345678A', 'email' => 'carlos.garcia@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 2, 'nombre' => 'María', 'apellidos' => 'López Sánchez', 'direccion' => 'Avenida Siempre Viva 742', 'telefono' => '600234567', 'dni' => '23456789B', 'email' => 'maria.lopez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 3, 'nombre' => 'Juan', 'apellidos' => 'Martínez Gómez', 'direccion' => 'Calle Mayor 1', 'telefono' => '600345678', 'dni' => '34567890C', 'email' => 'juan.martinez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 4, 'nombre' => 'Ana', 'apellidos' => 'Hernández Díaz', 'direccion' => 'Plaza del Sol 5', 'telefono' => '600456789', 'dni' => '45678901D', 'email' => 'ana.hernandez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 5, 'nombre' => 'Luis', 'apellidos' => 'González Fernández', 'direccion' => 'Calle Luna 8', 'telefono' => '600567890', 'dni' => '56789012E', 'email' => 'luis.gonzalez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 6, 'nombre' => 'Laura', 'apellidos' => 'Rodríguez Ruiz', 'direccion' => 'Calle Estrella 3', 'telefono' => '600678901', 'dni' => '67890123F', 'email' => 'laura.rodriguez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 7, 'nombre' => 'Pedro', 'apellidos' => 'López García', 'direccion' => 'Calle Sol 7', 'telefono' => '600789012', 'dni' => '78901234G', 'email' => 'pedro.lopez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 8, 'nombre' => 'Elena', 'apellidos' => 'Martín Sánchez', 'direccion' => 'Calle Mar 9', 'telefono' => '600890123', 'dni' => '89012345H', 'email' => 'elena.martin@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 9, 'nombre' => 'Javier', 'apellidos' => 'Jiménez Pérez', 'direccion' => 'Calle Río 11', 'telefono' => '600901234', 'dni' => '90123456I', 'email' => 'javier.jimenez@hotmail.com', 'password' => Hash::make('1234567')],
            ['clienteID' => 10, 'nombre' => 'Sara', 'apellidos' => 'García López', 'direccion' => 'Calle Montaña 13', 'telefono' => '600012345', 'dni' => '01234567J', 'email' => 'sara.garcia@hotmail.com', 'password' => Hash::make('1234567')],
        ];

        DB::table('clientes')->insert($clientes);
    }
}
