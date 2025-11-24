<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'ID_Proyecto';
    public $incrementing = false;   // pon true si tu PK es autoincrement
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ID_Proyecto',
        'Nombre',
        'Fecha',
        'Descripcion',
        'Certificado',
        'liquidado',
    ];

    protected $casts = [
        'Fecha'     => 'date:Y-m-d',
        'liquidado' => 'bool',
    ];
}
