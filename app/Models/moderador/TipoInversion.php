<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'tipo';        // Ajusta si tu tabla real difiere
    protected $primaryKey = 'ID_Tipo';
    public $timestamps = false;

    protected $fillable = ['Nombre', 'Descripcion'];
}
