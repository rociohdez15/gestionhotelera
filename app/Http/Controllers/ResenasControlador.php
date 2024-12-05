<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Reserva;
use App\Models\Resena;
use Carbon\Carbon;


class ResenasControlador extends Controller
{
    
    public function dejarResenas(Request $request, $clienteID = null)
    {
        
        if (!$clienteID) {
            $clienteID = Auth::id();
        }

        
        $hotelesSinResena = Hotel::select('hoteles.*', DB::raw('(SELECT imagen FROM imagenes_hoteles WHERE hotelID = hoteles.hotelID AND imagen LIKE "images/portadas/portada%" LIMIT 1) as imagen_url'))
            ->whereIn('hotelID', function ($habitaciones) use ($clienteID) {  
                $habitaciones->select('habitaciones.hotelID') 
                    ->from('reservas') 
                    ->join('habitaciones', 'reservas.habitacionID', '=', 'habitaciones.habitacionID') 
                    ->where('reservas.clienteID', $clienteID) 
                    ->whereNotExists(function ($resenas) use ($clienteID) { 
                        $resenas->select(DB::raw(1)) 
                            ->from('resenas') 
                            ->whereColumn('resenas.hotelID', 'habitaciones.hotelID') 
                            ->where('resenas.clienteID', $clienteID); 
                    });
            })
            ->get();

        
        $totalHoteles = $hotelesSinResena->count();

        
        $registros_por_pagina = 5; 
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHoteles / $registros_por_pagina);

        
        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        
        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        
        $datos_paginados = $hotelesSinResena->slice($inicio, $registros_por_pagina);

        
        $datos = $datos_paginados->values();

        $parametros = [
            "datos" => $datos,
            "pagina_actual" => $pagina_actual,
            "total_paginas" => $total_paginas,
            "registros_por_pagina" => $registros_por_pagina,
            'hotelesSinResena' => $hotelesSinResena
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('dejaresena', $parametros);
    }
    
    public function escribirResenasForm(Request $request, $hotelID)
    {
        
        $hotel = DB::table('hoteles')->where('hotelID', $hotelID)->first();

        
        $fechaHoy = Carbon::now()->format('Y-m-d');

        $parametros = [
            "mensajes" => [],
            "hotel" => $hotel,
            "fechaHoy" => $fechaHoy,
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('escribirresena', $parametros);
    }

    
    public function guardarResena(Request $request, $hotelID)
    {
        
        $request->validate([
            'clienteID' => 'required|exists:users,id',
            'hotelID' => 'required|exists:hoteles,hotelID',
            'fecha' => 'required|date',
            'resena' => 'required|string',
            'puntuacion' => 'required|integer|between:0,10',
        ]);

        $fechaHoy = Carbon::now()->format('Y-m-d');
        $clienteID = $request->input('clienteID');
        $cliente = User::find($clienteID)->name;

        
        $hotelID = $request->input('hotelID');
        $fecha = $fechaHoy;
        $texto = $request->input('resena');
        $puntuacion = $request->input('puntuacion');

        $resena = new Resena();
        $resena->clienteID = $clienteID;
        $resena->hotelID = $hotelID;
        $resena->nombre_cliente = $cliente;
        $resena->fecha = $fecha;
        $resena->texto = $texto;
        $resena->puntuacion = $puntuacion;
        $resena->save();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'Reseña registrada exitosamente.',
                'resena' => $resena
            ]);
        }

        return redirect()->route('dejarResenas')->with('success', 'Reseña registrada exitosamente.');
    }

    
    public function mostrarResenas($clienteID, Request $request)
    {
        
        $resenas = DB::table('resenas')
            ->join('hoteles', 'resenas.hotelID', '=', 'hoteles.hotelID') 
            ->leftJoin('imagenes_hoteles', function ($hoteles) { 
                $hoteles->on('hoteles.hotelID', '=', 'imagenes_hoteles.hotelID')
                    ->where('imagenes_hoteles.imagen', 'like', 'images/portadas/portada%'); 
            })
            ->select(
                'resenas.*', 
                'hoteles.nombre as hotel_nombre', 
                'imagenes_hoteles.imagen as hotel_imagen' 
            )
            ->where('resenas.clienteID', $clienteID) 
            ->get(); 

        
        $totalHoteles = $resenas->count();

        
        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHoteles / $registros_por_pagina);

        
        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        
        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        
        $datos_paginados = $resenas->slice($inicio, $registros_por_pagina);

        
        $datos = $datos_paginados->values();

        
        $parametros = [
            "mensajes" => [],
            "datos" => $datos,
            "pagina_actual" => $pagina_actual,
            "total_paginas" => $total_paginas,
            "registros_por_pagina" => $registros_por_pagina,
            "resenas" => $resenas,
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'resena' => $parametros
            ]);
        }
        return view('mostrarresenas', $parametros);
    }
}
