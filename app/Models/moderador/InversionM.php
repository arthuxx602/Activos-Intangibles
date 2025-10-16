<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inversion extends Model
{
    protected $table = 'inversion';
    protected $primaryKey = 'ID_Inversion';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Monto', 'Fecha', 'Descripcion',
        'FK_ID_Usuario', 'FK_ID_Proyecto',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'FK_ID_Usuario', 'ID_Usuario');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'FK_ID_Proyecto', 'ID_Proyecto');
    }
}
