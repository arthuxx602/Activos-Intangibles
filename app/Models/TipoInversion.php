<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInversion extends Model
{
    use HasFactory;

    protected $table = 'tipo'; // Nombre real de la tabla
    protected $primaryKey = 'ID_TIPO';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Descripcion',
    ];
}

