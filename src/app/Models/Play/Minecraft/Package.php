<?php

namespace App\Models\Play\Minecraft;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'minecraft_packages';

    public $timestamps = false;

    protected $fillable = [
        'prod',
        'build'
    ];
}
