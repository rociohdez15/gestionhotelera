<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InicioControlador extends Controller{
    public function buscarUbicaciones(Request $request){
        $query = $request->input('query');

        // Busca en las ciudades y nombres de hoteles
        $resultados = DB::table('hoteles')
            ->select('ciudad as nombre')
            ->where('ciudad', 'like', "%$query%")
            ->union(
                DB::table('hoteles')
                    ->select('nombre')
                    ->where('nombre', 'like', "%$query%")
            )
            ->distinct()
            ->get();

        return response()->json($resultados);
    }

    public function contarHoteles()
    {
        // Contar el número total de hoteles en cada ciudad
        $totalHotelesValencia = DB::table('hoteles')
            ->where('ciudad', 'Valencia')
            ->count();

        $totalHotelesMadrid = DB::table('hoteles')
            ->where('ciudad', 'Madrid')
            ->count();

        $totalHotelesMenorca = DB::table('hoteles')
            ->where('ciudad', 'Menorca')
            ->count();

        $totalHotelesSevilla = DB::table('hoteles')
            ->where('ciudad', 'Sevilla')
            ->count();

        // Pasar todos los conteos a la vista
        return view('inicio', [
            'totalHotelesValencia' => $totalHotelesValencia,
            'totalHotelesMadrid' => $totalHotelesMadrid,
            'totalHotelesMenorca' => $totalHotelesMenorca,
            'totalHotelesSevilla' => $totalHotelesSevilla,
        ]);
    }


    
}

?>