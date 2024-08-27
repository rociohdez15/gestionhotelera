<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaServicio extends Model
{
    use HasFactory;

    protected $table = 'reservas_servicios'; 

    protected $primaryKey = 'id'; 

    protected $fillable = [
        'reservaID',
        'servicioID',
    ];

    
    public $timestamps = false;
}