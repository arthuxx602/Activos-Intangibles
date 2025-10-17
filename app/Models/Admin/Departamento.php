<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamento';
    protected $primaryKey = 'ID_Departamento';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'FK_ID_Pais',
    ];

    // Relación: Departamento pertenece a un País
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'FK_ID_Pais', 'ID_Pais');
    }

    // Relación: Departamento tiene muchos Municipios
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'FK_ID_Departamento', 'ID_Departamento');
    }
}
