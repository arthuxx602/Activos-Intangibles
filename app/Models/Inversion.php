<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    protected $table = 'inversion2';
    protected $primaryKey = 'ID_Inversion';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Monto',
        'Monto_Ajustado',
        'Proyecto',              // si usas nombre de proyecto en esta columna (legacy)
        'FK_ID_Proyecto',        // si usas FK numérica (preferible). Mantén ambos si coexisten
        'FK_ID_Tipo',
        'FK_ID_Usuario',
        'Fecha',
        'Descripcion',
        'CertificadoInversion',
    ];

    // Relaciones (ajustar nombres/tablas si cambian)
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
