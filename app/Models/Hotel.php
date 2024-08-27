<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'hoteles'; 

    protected $primaryKey = 'hotelID'; 

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono',
        'descripcion',
    ];

    
    public $timestamps = false;
}