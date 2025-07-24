<?php

namespace App\Models\Security;

use App\Http\Controllers\Api\Security\TelegramController;
use Illuminate\Database\Eloquent\Model;

class Ack extends Model
{
    protected $table = 'security_ack';

    public $timestamps = true;

    protected $fillable = [
        'alarm_id',
        'user_id'
    ];

    public static function checkAlreayAcked($alarm, $user) {
        if(self::query()->where('alarm_id', '=', $alarm)->where('user_id', '=', $user)->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function sendAck($alarm_id, $user_id) {
        self::query()->create([
            'user_id' => $user_id,
            'alarm_id' => $alarm_id
        ]);
        $user = UnderSecurity::query()->where('id', '=', $user_id)->first();
        $api = new TelegramController();
        $alarm = Alarm::getAlarm($alarm_id);
        $owner_id = UserObjects::searchUserByObject($alarm->object_id);
        $owner = UnderSecurity::getUnderSecurityUser($owner_id->user_id);
        $text = $user->first_name.' '.$user->last_name.' отреагировал на сигнал тревоги по обьекту '.$alarm->object_id;
        $api->response(
            $api->builder($text, $owner->telegram_id)
        );
        $api->broadcast($text);
    }
}
