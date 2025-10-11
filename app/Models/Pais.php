<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'pais';
    protected $primaryKey = 'ID_Pais';
    public $timestamps = false;

    protected $fillable = ['ID_Pais','Nombre'];
}
