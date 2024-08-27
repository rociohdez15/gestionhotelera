<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios'; 

    protected $primaryKey = 'servicioID'; 

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'horario',
    ];

    
    public $timestamps = false;
}