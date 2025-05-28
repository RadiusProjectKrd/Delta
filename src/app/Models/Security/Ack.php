<?php

namespace app\Models\Security;

use Illuminate\Database\Eloquent\Model;

class Ack extends Model
{
    protected $table = 'security_ack';

    public $timestamps = true;

    protected $fillable = [
        'alarm_id',
        'user_id'
    ];
}
