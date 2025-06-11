<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;

class UnderSecurity extends Model
{
    protected $table = 'security_users';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'telegram_id',
        'first_name',
        'last_name'
    ];

    public static function getUnderSecurityUsers() {
        return self::query()->get()->all();
    }

    public static function getUnderSecurityUser($id) {
        return self::query()->where('id', '=', $id)->first();
    }
}
