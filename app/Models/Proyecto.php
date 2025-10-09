<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'ID_Proyecto';
    public $incrementing = true;
    protected $fillable = ['Nombre','Fecha']; // añade más columnas si las usas
}
