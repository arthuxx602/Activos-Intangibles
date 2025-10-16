<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Model
{
    // Tu tabla actual se llama "usuario2"
    protected $table = 'usuario2';

    // La PK es ID_Usuario (la ingresas manualmente con la cédula)
    protected $primaryKey = 'ID_Usuario';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'ID_Usuario', 'Nombre', 'Apellido', 'Telefono',
        'Correo', 'Contraseña', 'Fecha', 'FK_ID_Municipio', 'FK_ID_Rol'
    ];

    // Relaciones útiles para la vista
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'FK_ID_Municipio', 'ID_Municipio');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'FK_ID_Rol', 'ID_Rol');
    }

    public function proyectos(): BelongsToMany
    {
        return $this->belongsToMany(
            Proyecto::class,
            'proyecto_usuario',
            'FK_ID_Usuario',   // Foreign key de este modelo en la tabla pivote
            'FK_ID_Proyecto',  // Foreign key del otro modelo en la tabla pivote
            'ID_Usuario',      // Local key de este modelo
            'ID_Proyecto'      // Local key del otro modelo
        );
    }
}
