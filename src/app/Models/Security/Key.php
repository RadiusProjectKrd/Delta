<?php

namespace app\Models\Security;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $table = 'security_key';

    protected $fillable = [
        'object_id',
        'user_id',
        'key',
        'desc'
    ];

    public static function getByKey($key) {
        return self::query()->where('key', '=', $key)->first();
    }
}
