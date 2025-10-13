<?php

namespace App\Models\Inversionista;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    protected $table = 'inversion2';
    protected $primaryKey = 'ID_Inversion';
    public $timestamps = false;

    protected $fillable = [
        'Nombre','Monto','Monto_Ajustado','Proyecto','Tipo','Fecha',
        'Descripcion','CertificadoInversion','FK_ID_Usuario','FK_ID_Tipo','FK_ID_Proyecto'
    ];

    protected $casts = [
        'Fecha' => 'date:Y-m-d',
        'Monto' => 'decimal:2',
    ];
}
