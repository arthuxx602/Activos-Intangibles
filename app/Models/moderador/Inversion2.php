<?php

namespace App\Models\moderador;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inversion2 extends Model
{
    protected $table = 'inversion2';            // <-- tu tabla real
    protected $primaryKey = 'ID_Inversion2';    // <-- tu PK real
    public $incrementing = true;
    public $timestamps  = false;

    protected $fillable = [
        'Nombre',
        'Monto',
        'Monto_Ajustado',
        'proyecto',              // si esto es solo texto, lo dejamos así
        'Tipo',                  // idem, si es texto descriptivo
        'Fecha',
        'Descripcion',
        'CertificadoInversion',
        'FK_ID_Usuario',
        'FK_ID_Tipo',            // <-- revisa si aquí en la BD es FK_ID_Tipo o FK_ID_TIPO
        'FK_ID_Proyecto',        // agrégalo si existe en tu BD
    ];

    /**
     * Relación con el usuario dueño de la inversión.
     * Ajusta el modelo y las llaves según tu esquema real.
     */
    public function usuario(): BelongsTo
    {
        // si tienes App\Models\Usuario con PK ID_Usuario
        if (class_exists(\App\Models\Usuario::class)) {
            return $this->belongsTo(\App\Models\Usuario::class, 'FK_ID_Usuario', 'ID_Usuario');
        }

        // fallback al User por defecto de Laravel
        return $this->belongsTo(\App\Models\User::class, 'FK_ID_Usuario', 'id');
    }

    /**
     * Relación con el proyecto.
     * Ajusta nombres de modelo y llaves si es distinto en tu BD.
     */
    public function proyecto(): BelongsTo
    {
        if (class_exists(\App\Models\Proyecto::class)) {
            return $this->belongsTo(\App\Models\Proyecto::class, 'FK_ID_Proyecto', 'ID_Proyecto');
        }

        if (class_exists(\App\Models\Proyecto::class)) {
            return $this->belongsTo(\App\Models\Proyecto::class, 'FK_ID_Proyecto', 'ID_Proyecto');
        }

        // fallback genérico
        return $this->belongsTo(\App\Models\Proyecto::class, 'FK_ID_Proyecto', 'ID_Proyecto');
    }

    /**
     * Relación con el tipo de inversión.
     * AQUÍ ES DONDE TENÍAMOS EL PROBLEMA.
     */
    public function tipo(): BelongsTo
    {
        // tu modelo real es App\Models\TipoInversion
        // tu tabla real es 'tipo'
        // tu PK real es 'ID_TIPO'
        return $this->belongsTo(\App\Models\TipoInversion::class, 'FK_ID_Tipo', 'ID_TIPO');
        // si en tu BD la columna es FK_ID_TIPO (todo mayúsculas), cámbiala aquí.
    }

    /**
     * Helpers opcionales (solo si quieres acceder a texto plano guardado en columnas 'proyecto' / 'Tipo')
     */
    public function getProyectoNombreAttribute(): ?string
    {
        return $this->attributes['proyecto'] ?? null;
    }

    public function getTipoNombreAttribute(): ?string
    {
        return $this->attributes['Tipo'] ?? null;
    }
}
