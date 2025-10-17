<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'ID_Proyecto';
    public $timestamps = false;

    protected $fillable = [
        'Nombre', 'Fecha', 'Descripcion', 'Certificado'
    ];

    public function usuarios()
    {
        // pivot legacy: proyecto_usuario(FK_ID_Proyecto, FK_ID_Usuario)
        return $this->belongsToMany(Usuario::class, 'proyecto_usuario', 'FK_ID_Proyecto', 'FK_ID_Usuario');
    }
}
