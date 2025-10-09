<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario2';
    protected $primaryKey = 'ID_Usuario';
    public $incrementing = false;
    protected $keyType = 'int'; // si tu cédula es numérica; si es string, usa 'string'

    protected $fillable = ['Nombre','Apellido','FK_ID_Rol'];
}
