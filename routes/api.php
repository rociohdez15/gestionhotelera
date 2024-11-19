<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuscarHotelesControlador;
use App\Http\Controllers\DescubreEspanaControlador;
use App\Http\Controllers\OlvidoPasswordControlador;
use App\Http\Controllers\RealizarReservaControlador;
use App\Http\Controllers\InformacionUsuarioControlador;
use App\Http\Controllers\EditarPerfilControlador;
use App\Http\Controllers\ResenasControlador;
use App\Http\Controllers\ReservasControlador;
use App\Http\Controllers\PanelRecepcionistaControlador;
use App\Http\Controllers\ListarReservasControlador;
use App\Http\Controllers\ListadoCheckoutControlador;
use App\Http\Controllers\ListadoCheckinControlador;
use App\Http\Controllers\HabitacionesControlador;
use App\Http\Controllers\ListarServiciosControlador;
use App\Http\Controllers\ListarHotelesControlador;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/buscar-hoteles', [BuscarHotelesControlador::class, 'buscarhoteles'])->name('buscarHoteles');
Route::get('/descubreEspana/{ciudad}', [DescubreEspanaControlador::class, 'descubreEspana'])->name('descubreEspana');
Route::post('/olvido-password', [OlvidoPasswordControlador::class, 'olvidoPassword'])->name('olvidoPassword');
Route::get('/realizarreserva', [RealizarReservaControlador::class, 'realizarreserva'])->name('realizarreserva');
Route::post('/guardarreserva', [RealizarReservaControlador::class, 'guardarreserva'])->name('guardarreserva');
Route::get('/informacionusuario/{id}', [InformacionUsuarioControlador::class, 'mostrarinformacion'])->name('informacionusuario');
Route::get('/editarperfil/{clienteID}', [EditarPerfilControlador::class, 'mostrarPerfil'])->name('editarperfil');
Route::post('/editarperfil/{clienteID}/{id}', [EditarPerfilControlador::class, 'editarPerfil'])->name('editarPerfil');
Route::get('/dejarresena/{clienteID}', [ResenasControlador::class, 'dejarResenas'])->name('dejarResenas');
Route::post('/escribirresena/{hotelID}', [ResenasControlador::class, 'guardarResena'])->name('guardarResena');
Route::get('/mostrarresena/{clienteID}', [ResenasControlador::class, 'mostrarResenas'])->name('mostrarResenas');
Route::get('/misreservas/{clienteID}', [ReservasControlador::class, 'mostrarMisReservas'])->name('mostrarMisReservas');
Route::get('/listarreservas', [ListarReservasControlador::class, 'listarReservas'])->name('listarReservas');
Route::delete('/delreserva/{reservaID}', [ListarReservasControlador::class, 'delReserva'])->name('delReserva');
Route::get('/mostrarreserva/{reservaID}', [ListarReservasControlador::class, 'mostrarReserva'])->name('mostrarReserva');
Route::put('/editarreserva/{reservaID}', [ListarReservasControlador::class, 'editarReserva'])->name('editarReserva');
Route::get('/listadocheckout', [ListadoCheckoutControlador::class, 'listadoCheckout'])->name('listadoCheckout');
Route::get('/mostrarcheckout/{reservaID}', [ListadoCheckoutControlador::class, 'mostrarCheckout'])->name('mostrarCheckout');
Route::put('/registrarcheckout/{reservaID}', [ListadoCheckoutControlador::class, 'registrarCheckout'])->name('registrarCheckout');
Route::get('/listadocheckin', [ListadoCheckinControlador::class, 'listadoCheckin'])->name('listadoCheckin');
Route::get('/mostrarcheckin/{reservaID}', [ListadoCheckinControlador::class, 'mostrarCheckin'])->name('mostrarCheckin');
Route::put('/registrarcheckin/{reservaID}', [ListadoCheckinControlador::class, 'registrarCheckin'])->name('registrarCheckin');
Route::get('/listarservicios', [ListarServiciosControlador::class, 'listarServicios'])->name('listarServicios');
Route::delete('/delservicio/{servicioID}', [ListarServiciosControlador::class, 'delServicio'])->name('delServicio');
Route::get('/mostrarservicio/{servicioID}', [ListarServiciosControlador::class, 'mostrarServicio'])->name('mostrarServicio');
Route::put('/editarservicio/{servicioID}', [ListarServiciosControlador::class, 'editarServicio'])->name('editarServicio');
Route::get('/anadir-servicio', [ListarServiciosControlador::class, 'anadirServicio'])->name('anadirServicio');
Route::post('/guardarservicio', [ListarServiciosControlador::class, 'guardarServicio'])->name('guardarServicio');
Route::get('/gestionarhoteles', [ListarHotelesControlador::class, 'listarHoteles'])->name('listarHoteles');
Route::delete('/delhotel/{hotelID}', [ListarHotelesControlador::class, 'delHotel'])->name('delHotel');