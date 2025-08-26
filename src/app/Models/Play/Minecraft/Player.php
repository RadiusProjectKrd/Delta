<?php

namespace App\Models\Play\Minecraft;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'minecraft_player';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nickname',
        'uuid'
    ];
}
