<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';
    protected $primaryKey = 'ID_Departamento';
    public $timestamps = false;

    protected $fillable = ['Nombre','FK_ID_Pais'];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'FK_ID_Pais', 'ID_Pais');
    }
}
