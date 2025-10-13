<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rol;

class Usuario extends Model
{
    protected $table = 'usuario2';
    protected $primaryKey = 'ID_Usuario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ID_Usuario', 'Nombre', 'Apellido', 'Telefono', 'Correo',
        'Contraseña', 'Fecha', 'FK_ID_Municipio', 'FK_ID_Rol',
    ];

    protected $casts = [
        'Fecha' => 'date',
    ];

    // Relación opcional
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'FK_ID_Municipio', 'ID_Municipio')
            ->select(['ID_Municipio','Nombre','FK_ID_Departamento']);
    }

    public function rol()
    {
        return $this->belongsTo(\App\Models\Rol::class, 'FK_ID_Rol', 'ID_Rol')
            ->select(['ID_Rol','Nombre']);
    }
}
