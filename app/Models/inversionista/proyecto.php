<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'ID_Proyecto';
    public $timestamps = false;

    protected $fillable = ['ID_Proyecto','Nombre','Fecha','Descripcion','Certificado','liquidado'];

    public function inversiones()
    {
        return $this->hasMany(Inversion::class, 'FK_ID_Proyecto', 'ID_Proyecto');
    }
}
