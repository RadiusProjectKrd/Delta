<?php

namespace App\Models\Security;

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

    public static function getAlarm($id) {
        return self::query()->where('id', '=', $id)->first();
    }

    public static function openAlarm($alarm_id)
    {
        $api = new TelegramController();
        $alarm = self::getAlarm($alarm_id);
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $user_object_id = UserObjects::searchUserByObject($object_id)->user_id;
        $user_object = UnderSecurity::getUnderSecurityUser($user_object_id);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if (is_null($object->address)) {
                $text = "<b>ТРЕВОГА</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $user_object->first_name . " " . $user_object->last_name . "\n" .
                    "Тип: " . $object->type;
                $api->response(
                    $api->withButtons(
                        $api->builder($text, $user->telegram_id),
                        [
                            ['text' => 'Отреагировать', 'command' => 'ack ' . $alarm_id]
                        ],
                    )
                );
                $api->broadcast($text);
            } else {
                $text = "<b>ТРЕВОГА</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $user_object->first_name . " " . $user_object->last_name . "\n" .
                    "Адресс: " . $object->address . "\n" .
                    "Тип: " . $object->type;
                $api->response(
                    $api->withButtons(
                        $api->builder($text, $user->telegram_id),
                        [
                            ['text' => 'Отреагировать', 'command' => 'ack ' . $alarm_id]
                        ],
                    )
                );
                $api->broadcast($text);
            }
        }
    }

    public static function closeAlarm($alarm_id)
    {
        $api = new TelegramController();
        $alarm = self::getAlarm($alarm_id);
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $user_object_id = UserObjects::searchUserByObject($object_id)->user_id;
        $user_object = UnderSecurity::getUnderSecurityUser($user_object_id);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if (is_null($object->address)) {
                $text = "<b>Отбой тревоги</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $user_object->first_name . " " . $user_object->last_name . "\n" .
                    "Тип: " . $object->type;
                $api->response(
                    $api->builder($text, $user->telegram_id),
                );
                $api->broadcast($text);
            } else {
                $text = "<b>Отбой тревоги</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $user_object->first_name . " " . $user_object->last_name . "\n" .
                    "Адресс: " . $object->address . "\n" .
                    "Тип: " . $object->type;
                $api->response(
                    $api->builder($text, $user->telegram_id),
                );
                $api->broadcast($text);
            }
        }
    }
}
