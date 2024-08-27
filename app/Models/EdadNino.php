<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdadNino extends Model
{
    use HasFactory;

    protected $table = 'edadesninos'; 

    protected $primaryKey = 'edadesninosID'; 

    protected $fillable = [
        'edad',
        'reservaID',
    ];

    
    public $timestamps = false;
}