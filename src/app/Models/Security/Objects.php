<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
    protected $table = 'security_objects';

    public $timestamps = false;

    protected $fillable = [
        'object_id',
        'name',
        'address',
        'type',
        'state' // NotArmed - 0; Armed - 1; KTC - 2; Unknown 3+
    ];

    public static function getObject($object_id) {
        return self::query()->where('object_id', '=', $object_id)->firstOrFail();
    }
}
