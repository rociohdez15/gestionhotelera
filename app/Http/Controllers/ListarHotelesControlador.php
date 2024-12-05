<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Models\Cliente;
use App\Models\EdadNino;
use App\Models\Reserva;
use App\Models\Habitacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use TCPDF;

class ListarHotelesControlador extends Controller
{
    public function listarHoteles(Request $request)
    {
        $query = DB::table('hoteles')
            ->select('hotelID', 'nombre', 'direccion', 'ciudad', 'telefono', 'descripcion');

        $totalHoteles = $query->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHoteles / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $hoteles = $query->skip($inicio)->take($registros_por_pagina)->get();

        $parametros = [
            'hoteles' => $hoteles,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('listarhoteles', $parametros);
    }

    public function actualizarHoteles()
    {
        $hoteles = DB::table('hoteles')
            ->select('hotelID', 'nombre', 'direccion', 'ciudad', 'telefono', 'descripcion')
            ->paginate(5);

        return response()->json($hoteles);
    }

    public function delHotel($hotelID, Request $request)
    {
        
        $hotel = Hotel::find($hotelID);

        if (!$hotel) {
            return back()->withError('El hotel especificado no existe.');
        }

        
        DB::table('habitaciones')->where('hotelID', $hotelID)->delete();

        
        $hotel->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'El hotel se ha eliminado correctamente.',
                'hotel' => $hotel
            ]);
        }

        return redirect()->route('listarHoteles')->with('status', 'El hotel se ha eliminado correctamente.');
    }

    public function mostrarHotel(Request $request, $hotelID)
    {
        $hotel = Hotel::find($hotelID);

        if (!$hotel) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Hotel no encontrado'], 404);
            }
            return back()->withError('Hotel no encontrado');
        }

        $parametros = compact('hotel');

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($parametros);
        }

        return view('editarhotel', $parametros);
    }


    public function editarHotel(Request $request, $hotelID)
    {
        $hotel = Hotel::find($hotelID);
        if (!$hotel) {

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Hotel no encontrado'], 404);
            }
            return back()->withError('Hotel no encontrado');
        }

        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'descripcion' => 'nullable|string',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,svg,bmp,tiff,webp|max:2048', 
            ]);

            
            $hotel->nombre = $validatedData['nombre'];
            $hotel->direccion = $validatedData['direccion'];
            $hotel->ciudad = $validatedData['ciudad'];
            $hotel->telefono = $validatedData['telefono'];
            $hotel->descripcion = $validatedData['descripcion'] ?? null;
            $hotel->save();

            
            $hotel->refresh();

            
            if ($request->hasFile('imagenes')) {
                $imagenes = $request->file('imagenes');
                $ciudad = strtolower($hotel->ciudad);
                $rutaBase = "images/hoteles/{$ciudad}/hotel{$hotelID}";

                
                $ultimaImagen = DB::table('imagenes_hoteles')
                    ->where('hotelID', $hotelID)
                    ->orderBy('imagenID', 'desc')
                    ->first();

                $numeroImagen = $ultimaImagen ? intval(preg_replace('/[^0-9]/', '', $ultimaImagen->nombre_imagen)) : 0;

                foreach ($imagenes as $index => $imagen) {
                    $numeroImagen++;
                    $nombreImagen = "Hotel {$hotel->nombre} - Imagen {$numeroImagen}";
                    $extension = $imagen->getClientOriginalExtension();
                    $rutaImagen = "{$rutaBase}/imagen{$numeroImagen}.{$extension}";

                    
                    if (!is_dir(public_path($rutaBase))) {
                        mkdir(public_path($rutaBase), 0755, true);
                    }

                    
                    $imagen->move(public_path($rutaBase), "imagen{$numeroImagen}.{$extension}");

                    
                    $insercionExitosa = DB::table('imagenes_hoteles')->insert([
                        'hotelID' => $hotelID,
                        'imagen' => $rutaImagen,
                        'nombre_imagen' => $nombreImagen,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'El hotel se ha editado correctamente.',
                    'hotel' => $hotel
                ]);
            }

            
            $hoteles = Hotel::all();
            $pagina_actual = 'gestionarhoteles'; 
            $registros_por_pagina = 10; 
            $total_paginas = ceil($hoteles->count() / $registros_por_pagina); 

            return response()->json(['success' => 'El hotel se ha editado correctamente']);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al editar el hotel', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al editar el hotel')->withInput();
        }
    }

    public function generarPDF($hotelID)
    {
        
        $hotel = Hotel::join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->select(
                'hoteles.*',
                DB::raw('COUNT(habitaciones.habitacionID) as num_habitaciones')
            )
            ->where('hoteles.hotelID', $hotelID) 
            ->groupBy('hoteles.hotelID')
            ->first();

        
        $pdf = new TCPDF();

        
        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos del Hotel', 0, 1, 'C');

        
        $html = '<div style="font-size: 12px; text-align: center;">';

        
        if ($hotel) {
            $html .= '<div style="margin-bottom: 20px;">';
            $html .= '<strong>ID Hotel:</strong> ' . $hotel->hotelID . '<br>';
            $html .= '<strong>Nombre del Hotel:</strong> ' . $hotel->nombre . '<br>';
            $html .= '<strong>Dirección:</strong> ' . $hotel->direccion . '<br>';
            $html .= '<strong>Ciudad:</strong> ' . $hotel->ciudad . '<br>';
            $html .= '<strong>Teléfono:</strong> ' . $hotel->telefono . '<br>';
            $html .= '<strong>Descripción:</strong> ' . $hotel->descripcion . '<br>';
            $html .= '</div>';
        } else {
            $html .= '<div>No se encontraron datos para el hotel especificado.</div>';
        }

        $html .= '</div>';

        
        $pdf->writeHTML($html, true, false, true, false, '');

        
        $filename = "hotel_" . $hotelID . ".pdf";

        
        $pdf->Output($filename, 'D');
    }

    public function generarPDFTotal()
    {
        
        $hoteles = Hotel::join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->select(
                'hoteles.*',
                DB::raw('COUNT(habitaciones.habitacionID) as num_habitaciones')
            )
            ->groupBy('hoteles.hotelID')
            ->get();

        
        $pdf = new TCPDF();

        
        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Listado de Hoteles', 0, 1, 'C');

        
        $html = '<table border="1" style="font-size: 10px;">';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th>ID Hotel</th>';
        $html .= '<th>Nombre del Hotel</th>';
        $html .= '<th>Dirección</th>';
        $html .= '<th>Ciudad</th>';
        $html .= '<th>Teléfono</th>';
        $html .= '<th>Descripción</th>';
        $html .= '<th>Número de Habitaciones</th>';
        $html .= '</tr>';

        
        foreach ($hoteles as $hotel) {
            $html .= '<tr>';
            $html .= '<td>' . $hotel->hotelID . '</td>'; 
            $html .= '<td>' . $hotel->nombre . '</td>'; 
            $html .= '<td>' . $hotel->direccion . '</td>'; 
            $html .= '<td>' . $hotel->ciudad . '</td>'; 
            $html .= '<td>' . $hotel->telefono . '</td>'; 
            $html .= '<td>' . $hotel->descripcion . '</td>'; 
            $html .= '<td>' . $hotel->num_habitaciones . '</td>'; 
            $html .= '</tr>';
        }

        $html .= '</table>';

        
        $pdf->writeHTML($html, true, false, true, false, '');

        
        $filename = "lista_hoteles.pdf";

        
        $pdf->Output($filename, 'D');
    }

    public function buscadorHoteles(Request $request)
    {
        $query = $request->input('query');

        
        $consulta = Hotel::select('hoteles.*')
            ->leftJoin('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->where(function ($q) use ($query) {
                $q->where('hoteles.hotelID', 'LIKE', "%$query%")
                    ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
                    ->orWhere('hoteles.direccion', 'LIKE', "%$query%")
                    ->orWhere('hoteles.ciudad', 'LIKE', "%$query%")
                    ->orWhere('hoteles.telefono', 'LIKE', "%$query%")
                    ->orWhere('hoteles.descripcion', 'LIKE', "%$query%");
            })
            ->groupBy('hoteles.hotelID');
            $hoteles = $consulta->orderBy('hoteles.hotelID', 'asc')->paginate(5);


        if ($request->ajax()) {
            return response()->json($hoteles);
        }

        return view('listarhoteles', compact('hoteles'));
    }

    public function mostrarHoteles(Request $request)
    {
        
        $hoteles = Hotel::all();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['hoteles' => $hoteles]);
        }

        return view('anadirhotel', ['hoteles' => $hoteles]);
    }

    public function anadirHotel(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'descripcion' => 'nullable|string',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,svg,bmp,tiff,webp|max:2048', 
        ]);

        
        $hotel = new Hotel();
        $hotel->nombre = $validatedData['nombre'];
        $hotel->direccion = $validatedData['direccion'];
        $hotel->ciudad = $validatedData['ciudad'];
        $hotel->telefono = $validatedData['telefono'];
        $hotel->descripcion = $validatedData['descripcion'];
        $hotel->save();

        
        $hotel->refresh();

        
        if ($request->hasFile('imagenes')) {
            $imagenes = $request->file('imagenes');
            $ciudad = strtolower($hotel->ciudad);
            $rutaBase = "images/hoteles/{$ciudad}/hotel{$hotel->hotelID}";

            foreach ($imagenes as $index => $imagen) {
                $nombreImagen = "Hotel {$hotel->nombre} - Imagen " . ($index + 1);
                $rutaImagen = "{$rutaBase}/imagen" . ($index + 1) . ".{$imagen->getClientOriginalExtension()}";

                
                $imagen->move(public_path($rutaBase), "imagen" . ($index + 1) . ".{$imagen->getClientOriginalExtension()}");

                
                DB::table('imagenes_hoteles')->insert([
                    'hotelID' => $hotel->hotelID,
                    'nombre_imagen' => $nombreImagen,
                    'imagen' => $rutaImagen,
                ]);
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Hotel añadido correctamente.',
                'hotel' => $hotel
            ]);
        }

        return response()->json([
            'message' => 'Hotel añadido correctamente.',
            'hotel' => $hotel
        ]);
    }
}
