<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario2';
    protected $primaryKey = 'ID_Usuario';
    public $timestamps = false;

    // Si tu PK NO es autoincremental (cédula):
    // public $incrementing = false;
    // protected $keyType = 'int'; // o 'string'

    protected $fillable = ['Nombre','Apellido','Telefono','Correo','Contraseña','FK_ID_Municipio'];
}
