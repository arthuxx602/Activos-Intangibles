<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    protected $table = 'inversion2';
    protected $primaryKey = 'ID_Inversion';
    public $timestamps = false;

    protected $fillable = [
        'Nombre','Monto','Monto_Ajustado','Proyecto','FK_ID_Proyecto',
        'FK_ID_Usuario','FK_ID_Tipo','Fecha','Descripcion','CertificadoInversion'
    ];
}

