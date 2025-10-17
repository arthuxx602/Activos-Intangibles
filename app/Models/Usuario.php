<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuario2';
    protected $primaryKey = 'ID_Usuario';
    public $timestamps = false; // según tu schema

    protected $fillable = [
        'ID_Usuario','Nombre','Apellido','Telefono','Correo','Contraseña','Fecha','FK_ID_Municipio','FK_ID_Rol'
    ];

    protected $hidden = ['Contraseña'];

    // si tu columna de password se llama "Contraseña":
    public function getAuthPassword()
    {
        return $this->Contraseña;
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_usuario', 'FK_ID_Usuario', 'FK_ID_Proyecto');
    }
}
