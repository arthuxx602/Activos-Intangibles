<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ubicacion extends Model
{
    use HasFactory;

    // ADAPTA esto si tu tabla se llama distinto
    protected $table = 'ubicacion';
    protected $primaryKey = 'ID_Ubicacion'; // Si tu PK es "id", cambia a 'id' o elimina esta línea
    public $timestamps = false;

    protected $fillable = [
        'pais_id',
        'departamento_id',
        'municipio_id',
        'direccion',
    ];

    // Relaciones (ajusto FKs a tu esquema real)
    public function pais()
    {
        // pais_id (Ubicacion) -> ID_Pais (pais)
        return $this->belongsTo(Pais::class, 'pais_id', 'ID_Pais');
    }

    public function departamento()
    {
        // departamento_id (Ubicacion) -> ID_Departamento (departamento)
        return $this->belongsTo(Departamento::class, 'departamento_id', 'ID_Departamento');
    }

    public function municipio()
    {
        // municipio_id (Ubicacion) -> ID_Municipio (municipio)
        return $this->belongsTo(Municipio::class, 'municipio_id', 'ID_Municipio');
    }
}
