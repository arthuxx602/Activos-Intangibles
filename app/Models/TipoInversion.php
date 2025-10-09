<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoInversion extends Model
{
    protected $table = 'tipo';
    protected $primaryKey = 'ID_TIPO';
    public $timestamps = false;

    protected $fillable = ['Nombre','Descripcion'];
}
