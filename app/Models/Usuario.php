<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario2';
    protected $primaryKey = 'ID_Usuario';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_Usuario','Nombre','Apellido','Telefono','Correo','Contraseña','Fecha','FK_ID_Municipio','FK_ID_Rol'
    ];

    public function proyectos()
    {
        return $this->hasMany(ProyectoUsuario::class, 'FK_ID_Usuario', 'ID_Usuario');
    }
}
