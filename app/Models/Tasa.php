<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasa extends Model
{
    protected $table = 'tasa';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = ['Tasa'];
}
