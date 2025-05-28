<?php

namespace app\Models\Security;

use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
    protected $table = 'security_objects';

    public $timestamps = false;

    protected $fillable = [
        'object_id',
        'name',
        'address',
        'type'
    ];

    public static function getObject($object_id) {
        return self::query()->where('object_id', '=', $object_id)->firstOrFail();
    }
}
