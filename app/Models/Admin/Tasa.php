<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasa extends Model
{
    // Tabla y PK legacy
    protected $table      = 'tasa';
    protected $primaryKey = 'Id';
    public $timestamps    = false; // la tabla no usa created_at/updated_at

    protected $fillable = ['Tasa', 'Fecha'];

    protected $casts = [
        'Fecha' => 'datetime',
        'Tasa'  => 'float',
    ];
}
