<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'imagenes_hoteles'; 

    protected $primaryKey = 'habitacionID'; 

    protected $fillable = [
        'imagenID',
        'imagen',
        'nombre_imagen',
        'hotelID',
    ];

    
    public $timestamps = false;
}