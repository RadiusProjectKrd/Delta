<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class UserObjects extends Model
{
    protected $table = 'security_user_objects';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'object_id'
    ];

    public static function getAll($user) {
        return self::query()->where('user_id', '=', $user)->get();
    }

    public static function getOne($user, $object_id) {
        return self::query()->where('user_id', '=', $user)->where('object_id', '=', $object_id)->firstOrFail();
    }

    public static function searchUserByObject($object_id) {
        return self::query()->where('object_id', '=', $object_id)->first();
    }
}
