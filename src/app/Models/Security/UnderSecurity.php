<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UnderSecurity extends Model
{
    protected $table = 'security_users';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'telegram_id',
        'first_name',
        'last_name',
    ];

    public static function getUnderSecurityUsers()
    {
        return self::query()->get()->all();
    }

    public static function getUnderSecurityUser($id)
    {
        return self::query()->where('id', '=', $id)->first();
    }

    public function objects(): BelongsToMany
    {
        return $this->belongsToMany(
            Objects::class,
            'security_user_objects',
            'user_id',
            'object_id',
            'id',
            'object_id'
        );
    }
}
