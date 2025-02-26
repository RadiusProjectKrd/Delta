<?php

namespace App\Models\Play\Minecraft;

use Illuminate\Database\Eloquent\Model;

class RconMethod extends Model
{
    protected $table = 'rcon_methods';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'access'
    ];
}
