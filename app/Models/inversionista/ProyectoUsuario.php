<?php

namespace App\Models\Inversionista;

use Illuminate\Database\Eloquent\Model;

class ProyectoUsuario extends Model
{
    protected $table = 'proyecto_usuario';
    public $timestamps = false;

    protected $fillable = ['FK_ID_Usuario','FK_ID_Proyecto'];
}
