<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipio';
    protected $primaryKey = 'ID_Municipio';
    public $timestamps = false;

    protected $fillable = ['Nombre','FK_ID_Departamento'];
}
