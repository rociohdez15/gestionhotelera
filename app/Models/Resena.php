<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas'; 

    protected $primaryKey = 'resenaID'; 

    protected $fillable = [
        'clienteID',
        'hotelID',
        'nombre_cliente',
        'fecha',
        'texto',
        'puntuacion',
    ];

    
    public $timestamps = false;
}