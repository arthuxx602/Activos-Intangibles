<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'ID_Proyecto';
    public $timestamps = false;
    protected $fillable = ['Nombre','Fecha','Descripcion','Certificado'];
}
