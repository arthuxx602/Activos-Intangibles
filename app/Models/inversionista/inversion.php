<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    protected $table = 'inversion2';
    protected $primaryKey = 'ID_Inversion';
    public $timestamps = false;

    protected $fillable = [
        'Nombre', 'Monto', 'Monto_Ajustado', 'Proyecto', 'Tipo', 'Fecha',
        'Descripcion', 'CertificadoInversion', 'FK_ID_Usuario', 'FK_ID_Tipo', 'FK_ID_Proyecto'
    ];

    protected $casts = [
        'Fecha' => 'date:Y-m-d',
        'Monto' => 'float'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'FK_ID_Usuario', 'ID_Usuario');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'FK_ID_Proyecto', 'ID_Proyecto');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoInversion::class, 'FK_ID_Tipo', 'ID_TIPO');
    }
}
