<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'ID_Rol';
    public $timestamps = false;

    protected $fillable = ['ID_Rol','Nombre'];
}
