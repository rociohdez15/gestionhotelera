<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioControlador;
use App\Http\Controllers\BuscarHotelesControlador;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/inicio', function () {
    return redirect('/');
})->name('inicio');

Route::get('/buscarUbicaciones', [InicioControlador::class, 'buscarUbicaciones']);
Route::get('/', [InicioControlador::class, 'contarHoteles']);
Route::get('/buscar-hoteles', [BuscarHotelesControlador::class, 'buscarhoteles'])->name('buscarHoteles');
Route::get('/logout', [InicioControlador::class, 'logout'])->name('logout');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
});
