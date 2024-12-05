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
            
            $cliente = Cliente::find($id);
            if (!$cliente) {
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }
        } else {
            
            if (!Auth::check()) {
                return redirect()->route('login')->withErrors(['error' => 'Usuario no autenticado.']);
            }
            $correoUsuario = Auth::user()->email;
            $cliente = Cliente::where('email', $correoUsuario)->first();
        }
    
        
        $reservas = Reserva::where('clienteID', $cliente->clienteID)->take(3)->get();
    
        
        foreach ($reservas as $reserva) {
            $fechaEntrada = Carbon::parse($reserva->fechainicio);
            $fechaSalida = Carbon::parse($reserva->fechafin);
    
            
            $numDias = $fechaEntrada->diffInDays($fechaSalida);
    
            
            $reserva->num_dias = $numDias;
        }
    
        
        $resenas = DB::table('resenas')
            ->join('hoteles', 'resenas.hotelID', '=', 'hoteles.hotelID')
            ->join('imagenes_hoteles', function ($join) {
                $join->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%'); 
            })
            ->where('resenas.clienteID', $cliente->clienteID)
            ->select('resenas.*', 'hoteles.nombre', 'imagenes_hoteles.imagen as imagen_portada')
            ->take(2) 
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
