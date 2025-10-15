<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'pais';
    protected $primaryKey = 'ID_Pais';
    public $timestamps = false;

    protected $fillable = ['Nombre'];

    // Relación: País tiene muchos Departamentos
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'FK_ID_Pais', 'ID_Pais');
    }
}
