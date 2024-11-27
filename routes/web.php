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
use App\Http\Controllers\ListadoCheckoutControlador;
use App\Http\Controllers\ListadoCheckinControlador;
use App\Http\Controllers\HabitacionesControlador;
use App\Http\Controllers\ListarServiciosControlador;
use App\Http\Controllers\ListarHotelesControlador;
use App\Http\Controllers\ListarHabitacionesControlador;
use App\Http\Controllers\ListarUsuariosControlador;

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
Route::get('/sobre-nosotros', [InicioControlador::class, 'cargarSobreNosotros'])->name('cargarSobreNosotros');
Route::get('/contacto', [InicioControlador::class, 'cargarContacto'])->name('cargarContacto');

Route::middleware([
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
    Route::get('/realizarreserva', [RealizarReservaControlador::class, 'realizarreserva'])->name('realizarreserva');
    Route::get('/reservar', [BuscarHotelesControlador::class, 'reserve'])->name('reservar');
    Route::post('/guardarreserva', [RealizarReservaControlador::class, 'guardarreserva'])->name('guardarreserva');
    Route::get('/pagoreserva/{reservaIDs}', [RealizarReservaControlador::class, 'pagoreserva'])->name('pagoreserva');
    Route::get('/exitoreserva/{reservaIDs}', [RealizarReservaControlador::class, 'mostrarexito'])->name('exitoreserva');
    Route::get('/factura/{reservaIDs}', [RealizarReservaControlador::class, 'factura'])->name('factura');
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
    Route::get('/generar-pdf-listar-reservas/{reservaID}', [ListarReservasControlador::class, 'generarPDF'])->name('generar_pdf_listar_reservas');
    Route::get('/generar-pdf-listar-reservas-total', [ListarReservasControlador::class, 'generarPDFTotal'])->name('generar_pdf_listar_reservas_total');
    Route::get('/buscar-reservas', [ListarReservasControlador::class, 'buscarReservas'])->name('buscarReservas');
    Route::get('/listadocheckout', [ListadoCheckoutControlador::class, 'listadoCheckout'])->name('listadoCheckout');
    Route::get('/buscar-checkout', [ListadoCheckoutControlador::class, 'buscarCheckout'])->name('buscarCheckout');
    Route::get('/mostrarcheckout/{reservaID}', [ListadoCheckoutControlador::class, 'mostrarCheckout'])->name('mostrarCheckout');
    Route::put('/registrarcheckout/{reservaID}', [ListadoCheckoutControlador::class, 'registrarCheckout'])->name('registrarCheckout');
    Route::get('/listadocheckin', [ListadoCheckinControlador::class, 'listadoCheckin'])->name('listadoCheckin');
    Route::get('/buscar-checkin', [ListadoCheckinControlador::class, 'buscarCheckin'])->name('buscarCheckin');
    Route::get('/mostrarcheckin/{reservaID}', [ListadoCheckinControlador::class, 'mostrarCheckin'])->name('mostrarCheckin');
    Route::put('/registrarcheckin/{reservaID}', [ListadoCheckinControlador::class, 'registrarCheckin'])->name('registrarCheckin');
    Route::get('/disponibilidadHabitaciones', [HabitacionesControlador::class, 'dispHabitaciones'])->name('dispHabitaciones');
    Route::get('/listarservicios', [ListarServiciosControlador::class, 'listarServicios'])->name('listarServicios');
    Route::delete('/delservicio/{servicioID}', [ListarServiciosControlador::class, 'delServicio'])->name('delServicio');
    Route::get('/mostrarservicio/{servicioID}', [ListarServiciosControlador::class, 'mostrarServicio'])->name('mostrarServicio');
    Route::put('/editarservicio/{servicioID}', [ListarServiciosControlador::class, 'editarServicio'])->name('editarServicio');
    Route::get('/generar-pdf-listar-servicios/{servicioID}', [ListarServiciosControlador::class, 'generarPDF'])->name('generar_pdf_listar_servicios');
    Route::get('/generar-pdf-listar-servicios-total', [ListarServiciosControlador::class, 'generarPDFTotal'])->name('generar_pdf_listar_servicios_total');
    Route::get('/buscar-servicio', [ListarServiciosControlador::class, 'buscarServicios'])->name('buscarServicios');
    Route::get('/anadir-servicio', [ListarServiciosControlador::class, 'anadirServicio'])->name('anadirServicio');
    Route::post('/guardarservicio', [ListarServiciosControlador::class, 'guardarServicio'])->name('guardarServicio');
    Route::get('/gestionarhoteles', [ListarHotelesControlador::class, 'listarHoteles'])->name('listarHoteles');
    Route::delete('/delhotel/{hotelID}', [ListarHotelesControlador::class, 'delHotel'])->name('delHotel');
    Route::get('/mostrarhotel/{hotelID}', [ListarHotelesControlador::class, 'mostrarHotel'])->name('mostrarHotel');
    Route::put('/editarhotel/{hotelID}', [ListarHotelesControlador::class, 'editarHotel'])->name('editarHotel');
    Route::get('/generar-pdf-listar-hoteles/{hotelID}', [ListarHotelesControlador::class, 'generarPDF'])->name('generar_pdf_listar_hoteles');
    Route::get('/generar-pdf-listar-hoteles-total', [ListarHotelesControlador::class, 'generarPDFTotal'])->name('generar_pdf_listar_hoteles_total');
    Route::get('/buscador-hoteles', [ListarHotelesControlador::class, 'buscadorHoteles'])->name('buscadorHoteles');
    Route::get('/mostrar-hoteles', [ListarHotelesControlador::class, 'mostrarHoteles'])->name('mostrarHoteles');
    Route::post('/anadir-hoteles', [ListarHotelesControlador::class, 'anadirHotel'])->name('anadirHotel');
    Route::get('/gestionarhabitaciones', [ListarHabitacionesControlador::class, 'listarHabitaciones'])->name('listarHabitaciones');
    Route::get('/buscar-habitaciones', [ListarHabitacionesControlador::class, 'buscarHabitaciones'])->name('buscarHabitaciones');
    Route::delete('/delhabitacion/{habitacionID}', [ListarHabitacionesControlador::class, 'delHabitacion'])->name('delHabitacion');
    Route::get('/mostrar-habitaciones', [ListarHabitacionesControlador::class, 'mostrarHabitaciones'])->name('mostrarHabitaciones');
    Route::post('/anadir-habitaciones', [ListarHabitacionesControlador::class, 'anadirHabitacion'])->name('anadirHabitacion');
    Route::get('/generar-pdf-listar-habitaciones/{habitacionlID}', [ListarHabitacionesControlador::class, 'generarPDF'])->name('generar_pdf_listar_habitaciones');
    Route::get('/generar-pdf-listar-habitaciones-total', [ListarHabitacionesControlador::class, 'generarPDFTotal'])->name('generar_pdf_listar_habitaciones_total');
    Route::get('/mostrarhabitacion/{habitacionID}', [ListarHabitacionesControlador::class, 'mostrarHabitacion'])->name('mostrarHabitacion');
    Route::put('/editarhabitacion/{habitacionID}', [ListarHabitacionesControlador::class, 'editarHabitacion'])->name('editarHabitacion');
    Route::get('/listarusuarios', [ListarUsuariosControlador::class, 'listarUsuarios'])->name('listarUsuarios');
    Route::get('/buscar-usuarios', [ListarUsuariosControlador::class, 'buscarUsuarios'])->name('buscarUsuarios');
    Route::delete('/delusuario/{id}', [ListarUsuariosControlador::class, 'delUsuario'])->name('delUsuario');
    Route::get('/mostrarusuario/{id}', [ListarUsuariosControlador::class, 'mostrarUsuario'])->name('mostrarUsuario');
    Route::put('/editarusuario/{id}', [ListarUsuariosControlador::class, 'editarUsuario'])->name('editarUsuario');
    Route::get('/mostrar-usuarios', [ListarUsuariosControlador::class, 'mostrarUsuarios'])->name('mostrarUsuarios');
    Route::post('/anadir-usuarios', [ListarUsuariosControlador::class, 'anadirUsuario'])->name('anadirUsuario');
    Route::get('/generar-pdf-listar-usuarios/{id}', [ListarUsuariosControlador::class, 'generarPDF'])->name('generar_pdf_listar_usuarios');
    Route::get('/generar-pdf-listar-usuarios-total', [ListarUsuariosControlador::class, 'generarPDFTotal'])->name('generar_pdf_listar_usuarios_total');
    Route::get('/comprobar-email', [ListarUsuariosControlador::class, 'comprobarEmail']);
    Route::get('/actualizar-reservas', [ListarReservasControlador::class, 'actualizarReservas'])->name('actualizarReservas');
    Route::get('/actualizar-hoteles', [ListarHotelesControlador::class, 'actualizarHoteles'])->name('actualizarHoteles');
    Route::get('/actualizar-habitaciones', [ListarHabitacionesControlador::class, 'actualizarHabitaciones'])->name('actualizarHabitacions');
});
