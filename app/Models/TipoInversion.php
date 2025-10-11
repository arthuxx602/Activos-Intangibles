<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInversion extends Model
{
    use HasFactory;

    protected $table = 'tipo'; // coincide con tu base de datos legacy
    protected $primaryKey = 'ID_Tipo';
    public $timestamps = false;

    protected $fillable = ['Nombre', 'Descripcion'];
}
