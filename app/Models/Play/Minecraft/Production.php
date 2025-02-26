<?php

namespace App\Models\Play\Minecraft;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $table = 'prods';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'ver'
    ];
}
