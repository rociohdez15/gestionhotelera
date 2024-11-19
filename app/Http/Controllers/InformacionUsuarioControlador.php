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
    public function mostrarinformacion(Request $request, $id = null)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            // Para solicitudes de API, usa el ID del usuario pasado como parámetro
            $cliente = Cliente::find($id);
            if (!$cliente) {
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }
        } else {
            // Para solicitudes de vista, usa el usuario autenticado
            if (!Auth::check()) {
                return redirect()->route('login')->withErrors(['error' => 'Usuario no autenticado.']);
            }
            $correoUsuario = Auth::user()->email;
            $cliente = Cliente::where('email', $correoUsuario)->first();
        }
    
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
    
        // Obtiene las reseñas del cliente, junto con el nombre y la imagen del hotel
        $resenas = DB::table('resenas')
            ->join('hoteles', 'resenas.hotelID', '=', 'hoteles.hotelID')
            ->join('imagenes_hoteles', function ($join) {
                $join->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%'); // Solo selecciona la imagen de portada
            })
            ->where('resenas.clienteID', $cliente->clienteID)
            ->select('resenas.*', 'hoteles.nombre', 'imagenes_hoteles.imagen as imagen_portada')
            ->take(2) // Selecciona solo las 2 primeras reseñas
            ->get();
    
        $parametros = [
            'cliente' => $cliente,
            'reservas' => $reservas,
            'resenas' => $resenas
        ];
    
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }
    
        return view('informacionusuario', $parametros);
    }
}
