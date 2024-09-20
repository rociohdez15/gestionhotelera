<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\Resena;
use Carbon\Carbon;


class InformacionUsuarioControlador extends Controller
{
    public function mostrarinformacion(Request $request)
    {
        // Obtiene el correo del usuario autenticado
        $correoUsuario = Auth::user()->email;

        // Busca al cliente que tiene el mismo correo que el usuario autenticado
        $cliente = Cliente::where('email', $correoUsuario)->first();

        // Obtiene las primeras 3 reservas del cliente
        $reservas = Reserva::where('clienteID', $cliente->clienteID)->take(3)->get();

        // Calcula el número de días para cada reserva
        foreach ($reservas as $reserva) {
            $fechaEntrada = Carbon::parse($reserva->fechainicio);
            $fechaSalida = Carbon::parse($reserva->fechafin);

            // Calcula el número de días entre las dos fechas
            $numDias = $fechaEntrada->diffInDays($fechaSalida);

            // Añade el número de días a la reserva
            $reserva->num_dias = $numDias;
        }

        // Obtiene las reseñas del cliente, uniendo con las tablas hoteles y la tabla imagenes_hoteles
        $resenas = DB::table('resenas')
            ->join('hoteles', 'resenas.hotelID', '=', 'hoteles.hotelID')
            ->join('imagenes_hoteles', function ($join) {
                $join->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%'); // Solo selecciona la imagen de portada
            })
            ->where('resenas.clienteID', $cliente->clienteID)
            ->select('resenas.*', 'hoteles.nombre', 'imagenes_hoteles.imagen as imagen_portada')
            ->take(2) // Limitar a las 3 primeras reseñas
            ->get();

        // Pasa todas las variables a la vista en un solo array
        return view('informacionusuario', [
            'cliente' => $cliente,
            'reservas' => $reservas,
            'resenas' => $resenas
        ]);
    }
}
