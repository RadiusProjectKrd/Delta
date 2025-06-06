<?php

namespace app\Models\Security;

use App\Http\Controllers\Api\Security\TelegramController;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    protected $table = 'security_alarm';

    public $timestamps = true;

    protected $fillable = [
        'object_id',
        'state'
    ];

    public static function openAlarm($alarm_id)
    {
        $api = new TelegramController();
        $alarm = Alarm::query()->where('id', '=', $alarm_id)->first();
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $user_object_id = UserObjects::searchUserByObject($object_id)->user_id;
        $user_object = UnderSecurity::getUnderSecurityUser($user_object_id);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if (is_null($object->address)) {
                $api->response(
                    $api->withButtons(
                        $api->builder(
                            "<b>ТРЕВОГА</b>" .
                            "Номер обьекта: " . $object_id .
                            "Пользователь: " . $user_object->first_name . " " . $user_object->last_name .
                            "Тип: " . $object->type,
                            $user->telegram_id),
                        [
                            ['text' => 'Отреагировать', 'command' => 'ack ' . $alarm_id]
                        ],
                    )
                );
            } else {
                $api->response(
                    $api->withButtons(
                        $api->builder(
                            "<b>ТРЕВОГА</b>" .
                            "Номер обьекта: " . $object_id .
                            "Пользователь: " . $user_object->first_name . " " . $user_object->last_name .
                            "Адресс: " . $object->address .
                            "Тип: " . $object->type,
                            $user->telegram_id),
                        [
                            ['text' => 'Отреагировать', 'command' => 'ack ' . $alarm_id]
                        ],
                    )
                );
            }
        }
    }

    public static function closeAlarm($alarm_id)
    {
        $api = new TelegramController();
        $alarm = Alarm::query()->where('id', '=', $alarm_id)->first();
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $user_object_id = UserObjects::searchUserByObject($object_id)->user_id;
        $user_object = UnderSecurity::getUnderSecurityUser($user_object_id);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if (is_null($object->address)) {
                $api->response(
                    $api->builder(
                        "<b>Отбой тревоги</b>" .
                        "Номер обьекта: " . $object_id .
                        "Пользователь: " . $user_object->first_name . " " . $user_object->last_name .
                        "Тип: " . $object->type,
                        $user->telegram_id),
                );
            } else {
                $api->response(
                    $api->builder(
                        "<b>Отбой тревоги</b>" .
                        "Номер обьекта: " . $object_id .
                        "Пользователь: " . $user_object->first_name . " " . $user_object->last_name .
                        "Адресс: " . $object->address .
                        "Тип: " . $object->type,
                        $user->telegram_id),
                );
            }
        }
    }
}
