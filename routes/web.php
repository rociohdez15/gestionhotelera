<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioControlador;
use App\Http\Controllers\BuscarHotelesControlador;
use App\Http\Controllers\DescubreEspanaControlador;
use App\Http\Controllers\InformacionUsuarioControlador;
use App\Http\Controllers\OlvidoPasswordControlador;
use App\Http\Controllers\RealizarReservaControlador;
use App\Http\Controllers\EditarPerfilControlador;
use App\Http\Controllers\ResenasControlador;
use App\Http\Controllers\ReservasControlador;
use App\Http\Controllers\PanelRecepcionistaControlador;
use App\Http\Controllers\ListarReservasControlador;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/inicio', function () {
    return redirect('/');
})->name('inicio');

Route::get('/buscarUbicaciones', [InicioControlador::class, 'buscarUbicaciones']);
Route::get('/', [InicioControlador::class, 'contarHoteles']);
Route::get('/buscar-hoteles', [BuscarHotelesControlador::class, 'buscarhoteles'])->name('buscarHoteles');
Route::get('/descubreEspana/{ciudad}', [DescubreEspanaControlador::class, 'descubreEspana'])->name('descubreEspana');
Route::get('/olvido-password', function () { return view('olvidopassword'); })->name('olvidoPassword');
Route::post('/olvido-password', [OlvidoPasswordControlador::class, 'olvidoPassword'])->name('olvidoPassword');
Route::get('/logout', [InicioControlador::class, 'logout'])->name('logout');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
    Route::get('/realizarreserva', [RealizarReservaControlador::class, 'realizarreserva'])->name('realizarreserva');
    Route::get('/reservar', [BuscarHotelesControlador::class, 'reserve'])->name('reservar');
    Route::post('/guardarreserva', [RealizarReservaControlador::class, 'guardarreserva'])->name('guardarreserva');
    Route::get('/exitoreserva', [RealizarReservaControlador::class, 'mostrarexito'])->name('exitoreserva');
    Route::get('/informacionusuario', [InformacionUsuarioControlador::class, 'mostrarinformacion'])->name('informacionusuario');
    Route::get('/editarperfil/{clienteID}', [EditarPerfilControlador::class, 'mostrarPerfil'])->name('editarperfil');
    Route::post('/editarperfil/{clienteID}/{id}', [EditarPerfilControlador::class, 'editarPerfil'])->name('editarPerfil');
    Route::get('/dejarresena', [ResenasControlador::class, 'dejarResenas'])->name('dejarResenas');
    Route::get('/escribirresena/{hotelID}', [ResenasControlador::class, 'escribirResenasForm'])->name('escribirResenasForm');
    Route::post('/escribirresena', [ResenasControlador::class, 'guardarResena'])->name('guardarResena');
    Route::get('/mostrarresena/{clienteID}', [ResenasControlador::class, 'mostrarResenas'])->name('mostrarResenas');
    Route::get('/misreservas', [ReservasControlador::class, 'mostrarMisReservas'])->name('mostrarMisReservas');
    Route::get('/panelrecepcionista', [PanelRecepcionistaControlador::class, 'mostrarPanel'])->name('panelrecepcionista');
    Route::get('/listarreservas', [ListarReservasControlador::class, 'listarReservas'])->name('listarReservas');
    Route::delete('/delreserva/{reservaID}', [ListarReservasControlador::class, 'delReserva'])->name('delReserva');
    Route::get('/mostrarreserva/{reservaID}', [ListarReservasControlador::class, 'mostrarReserva'])->name('mostrarReserva');
    Route::put('/editarreserva/{reservaID}', [ListarReservasControlador::class, 'editarReserva'])->name('editarReserva');
    Route::get('/comprobar-reserva/{hotelID}', [ListarReservasControlador::class, 'comprobarReserva']);
    Route::get('/verificar-habitacion/{hotelID}', [ListarReservasControlador::class, 'verificarHabitacion']);
    Route::get('/generar-pdf-listar-reservas', [ListarReservasControlador::class, 'generarPDF'])->name('generar_pdf_listar_reservas');
    Route::get('/buscar-reservas', [ListarReservasControlador::class, 'buscarReservas'])->name('buscarReservas');
});
