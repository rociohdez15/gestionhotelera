<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas'; 

    protected $primaryKey = 'reservaID'; 

    protected $fillable = [
        'fechainicio',
        'fechafin',
        'estado',
        'preciototal',
        'num_adultos',
        'num_ninos',
        'fecha_checkin',
        'fecha_checkout',
        'clienteID',
        'habitacionID',
    ];

    
    public $timestamps = false;
}