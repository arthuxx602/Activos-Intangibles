<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    protected $table = 'inversion2';
    protected $primaryKey = 'ID_Inversion';
    public $incrementing = true;

    protected $fillable = [
        'Nombre','Monto','Monto_Ajustado','proyecto','Tipo','Fecha','Descripcion',
        'CertificadoInversion','FK_ID_Usuario','FK_ID_Tipo'
    ];
}
