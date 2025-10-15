<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipio';
    protected $primaryKey = 'ID_Municipio';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'FK_ID_Departamento',
    ];

    // Relación: Municipio pertenece a un Departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'FK_ID_Departamento', 'ID_Departamento');
    }

    // Acceso rápido al País del Municipio (a través del Departamento)
    public function pais()
    {
        return $this->hasOneThrough(
            Pais::class,
            Departamento::class,
            'ID_Departamento',     // Clave local en Departamento
            'ID_Pais',             // Clave local en País
            'FK_ID_Departamento',  // FK en Municipio que apunta a Departamento
            'FK_ID_Pais'           // FK en Departamento que apunta a País
        );
    }
}
