<?php

namespace App\Models\Play\Minecraft;

use Illuminate\Database\Eloquent\Model;

class RconMethod extends Model
{
    protected $table = 'minecraft_rcon_methods';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'access'
    ];
}
