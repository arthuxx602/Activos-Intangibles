<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'pais';           // cambia a 'paises' si tu tabla es plural
    protected $primaryKey = 'ID_Pais';
    public $timestamps = false;

    protected $fillable = ['Nombre'];

    // (opcional) relación inversa
    public function departamentos()
    {
        return $this->hasMany(\App\Models\Departamento::class, 'FK_ID_Pais', 'ID_Pais');
    }
}
