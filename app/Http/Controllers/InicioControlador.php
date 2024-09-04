<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


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


    public function listhoteles(Request $request){
        
        $ubicacion = $request->input('ubicacion');
        $fechaEntrada = $request->input('fecha_entrada');
        $fechaSalida = $request->input('fecha_salida');
        $adultos = $request->input('adultos');
        $ninos = $request->input('ninos');
        $habitaciones = $request->input('habitaciones');

        // Inicia la consulta de los hoteles
        $query = DB::table('hoteles')->select('hoteles.*');

        // Aplica filtros según los parámetros de búsqueda
        if (!empty($ubicacion)) {
            $query->where(function($q) use ($ubicacion) {
                $q->where('ciudad', 'like', '%' . $ubicacion . '%')
                  ->orWhere('nombre', 'like', '%' . $ubicacion . '%');
            });
        }

        $hoteles = $query->get();

        $parametros = [
            "tituloventana" => "AlojaDirecto | Inicio",
            "datos" => $hoteles,
            "mensajes" => [],
        ];

        $parametros["mensajes"][] = [
            "tipo" => "success",
            "mensaje" => "El listado se realizó correctamente"
        ];

        return view('listhoteles', $parametros); // Muestra la vista de listar hoteles
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
    }
}

?>