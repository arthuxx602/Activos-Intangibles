<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion2 extends Model
{
    protected $table = 'inversion2';
    protected $primaryKey = 'ID_Inversion2'; // Ajusta si tu PK real tiene otro nombre
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Monto',
        'Monto_Ajustado',
        'proyecto',
        'Tipo',
        'Fecha',
        'Descripcion',
        'CertificadoInversion',
        'FK_ID_Usuario',
        'FK_ID_Tipo',
    ];
}
