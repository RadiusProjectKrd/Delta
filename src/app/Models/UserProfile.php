<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'userdata';

    public $timestamps = false;

    protected $fillable = [
        'minecraft',
    ];
}
