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
    public function delHotel($hotelID, Request $request)
    {
        // Buscar el hotel
        $hotel = Hotel::find($hotelID);

        if (!$hotel) {
            return back()->withError('El hotel especificado no existe.');
        }

        // Eliminar las habitaciones asociadas al hotel
        DB::table('habitaciones')->where('hotelID', $hotelID)->delete();

        // Eliminar el hotel
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
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,svg,bmp,tiff,webp|max:2048', // Validar imágenes
            ]);

            // Actualizar los datos del hotel
            $hotel->nombre = $validatedData['nombre'];
            $hotel->direccion = $validatedData['direccion'];
            $hotel->ciudad = $validatedData['ciudad'];
            $hotel->telefono = $validatedData['telefono'];
            $hotel->descripcion = $validatedData['descripcion'] ?? null;
            $hotel->save();

            // Recargar el hotel para obtener los datos actualizados
            $hotel->refresh();

            // Manejar la subida de imágenes
            if ($request->hasFile('imagenes')) {
                $imagenes = $request->file('imagenes');
                $ciudad = strtolower($hotel->ciudad);
                $rutaBase = "images/hoteles/{$ciudad}/hotel{$hotelID}";

                // Obtener el número de la última imagen para continuar la numeración
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

                    // Crear el directorio si no existe
                    if (!is_dir(public_path($rutaBase))) {
                        mkdir(public_path($rutaBase), 0755, true);
                    }

                    // Guardar la imagen en el almacenamiento
                    $imagen->move(public_path($rutaBase), "imagen{$numeroImagen}.{$extension}");

                    // Guardar la información de la imagen en la base de datos
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

            // Obtener la lista de hoteles para pasarla a la vista
            $hoteles = Hotel::all();
            $pagina_actual = 'gestionarhoteles'; // Define la variable $pagina_actual
            $registros_por_pagina = 10; // Define la variable $registros_por_pagina
            $total_paginas = ceil($hoteles->count() / $registros_por_pagina); // Define la variable $total_paginas

            // Redirigir a la vista con el mensaje de estado, la lista de hoteles, la página actual y el total de páginas
            return view('listarhoteles')->with([
                'status' => 'El hotel se ha editado correctamente.',
                'hoteles' => $hoteles,
                'pagina_actual' => $pagina_actual,
                'total_paginas' => $total_paginas,
                'registros_por_pagina' => $registros_por_pagina
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error al editar el hotel', 'error' => $e->getMessage()], 500);
            }
            return back()->withError('Error al editar el hotel')->withInput();
        }
    }

    public function generarPDF($hotelID)
    {
        // Consulta para obtener los datos del hotel específico
        $hotel = Hotel::join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->select(
                'hoteles.*',
                DB::raw('COUNT(habitaciones.habitacionID) as num_habitaciones')
            )
            ->where('hoteles.hotelID', $hotelID) // Filtrar por hotelID
            ->groupBy('hoteles.hotelID')
            ->first();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de los datos
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Datos del Hotel', 0, 1, 'C');

        // Crear el contenido en el PDF
        $html = '<div style="font-size: 12px; text-align: center;">';

        // Agregar los datos del hotel al contenido del PDF
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

        // Agregar el contenido al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "hotel_" . $hotelID . ".pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function generarPDFTotal()
    {
        // Consulta para generar un pdf del listado de hoteles
        $hoteles = Hotel::join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->select(
                'hoteles.*',
                DB::raw('COUNT(habitaciones.habitacionID) as num_habitaciones')
            )
            ->groupBy('hoteles.hotelID')
            ->get();

        // Crear un nuevo objeto TCPDF
        $pdf = new TCPDF();

        // Agregar una página
        $pdf->AddPage();

        // Añadir título antes de la tabla
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Listado de Hoteles', 0, 1, 'C');

        // Crear la tabla en el PDF 
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

        // Iterar sobre los datos de los hoteles y agregarlos a la tabla del PDF
        foreach ($hoteles as $hotel) {
            $html .= '<tr>';
            $html .= '<td>' . $hotel->hotelID . '</td>'; // ID del hotel
            $html .= '<td>' . $hotel->nombre . '</td>'; // Nombre del hotel
            $html .= '<td>' . $hotel->direccion . '</td>'; // Dirección
            $html .= '<td>' . $hotel->ciudad . '</td>'; // Ciudad
            $html .= '<td>' . $hotel->telefono . '</td>'; // Teléfono
            $html .= '<td>' . $hotel->descripcion . '</td>'; // Descripción
            $html .= '<td>' . $hotel->num_habitaciones . '</td>'; // Número de habitaciones
            $html .= '</tr>';
        }

        $html .= '</table>';

        // Agregar la tabla al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nombre del archivo PDF
        $filename = "lista_hoteles.pdf";

        // Salida del PDF al navegador
        $pdf->Output($filename, 'D');
    }

    public function buscadorHoteles(Request $request)
    {
        $query = $request->input('query');

        // Consulta para utilizar el buscador en el listado de hoteles
        $consulta = Hotel::select('hoteles.*', DB::raw('COUNT(habitaciones.habitacionID) as num_habitaciones'))
            ->join('habitaciones', 'hoteles.hotelID', '=', 'habitaciones.hotelID')
            ->where('hoteles.hotelID', 'LIKE', "%$query%")
            ->orWhere('hoteles.nombre', 'LIKE', "%$query%")
            ->orWhere('hoteles.direccion', 'LIKE', "%$query%")
            ->orWhere('hoteles.ciudad', 'LIKE', "%$query%")
            ->orWhere('hoteles.telefono', 'LIKE', "%$query%")
            ->orWhere('hoteles.descripcion', 'LIKE', "%$query%")
            ->groupBy('hoteles.hotelID');

        $totalHoteles = $consulta->count();

        $registros_por_pagina = 5;
        $pagina_actual = $request->input('pagina', 1);
        $total_paginas = ceil($totalHoteles / $registros_por_pagina);

        if ($pagina_actual < 1) $pagina_actual = 1;
        if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;

        $inicio = ($pagina_actual - 1) * $registros_por_pagina;

        $hoteles = $consulta->skip($inicio)->take($registros_por_pagina)->get();

        return view('listarhoteles', [
            'hoteles' => $hoteles,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros_por_pagina' => $registros_por_pagina
        ], compact('hoteles'));
    }

    public function mostrarHoteles(Request $request)
    {
        // Obtén todos los hoteles desde la base de datos
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
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,svg,bmp,tiff,webp|max:2048', // Validar imágenes
        ]);

        // Crear el nuevo hotel
        $hotel = new Hotel();
        $hotel->nombre = $validatedData['nombre'];
        $hotel->direccion = $validatedData['direccion'];
        $hotel->ciudad = $validatedData['ciudad'];
        $hotel->telefono = $validatedData['telefono'];
        $hotel->descripcion = $validatedData['descripcion'];
        $hotel->save();

        // Recargar el hotel para obtener el ID generado
        $hotel->refresh();

        // Manejar la subida de imágenes
        if ($request->hasFile('imagenes')) {
            $imagenes = $request->file('imagenes');
            $ciudad = strtolower($hotel->ciudad);
            $rutaBase = "images/hoteles/{$ciudad}/hotel{$hotel->hotelID}";

            foreach ($imagenes as $index => $imagen) {
                $nombreImagen = "Hotel {$hotel->nombre} - Imagen " . ($index + 1);
                $rutaImagen = "{$rutaBase}/imagen" . ($index + 1) . ".{$imagen->getClientOriginalExtension()}";

                // Guardar la imagen en el almacenamiento
                $imagen->move(public_path($rutaBase), "imagen" . ($index + 1) . ".{$imagen->getClientOriginalExtension()}");

                // Guardar la información de la imagen en la base de datos
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

        return redirect()->route('listarHoteles')->with('success', 'Hotel añadido correctamente');
    }
}
