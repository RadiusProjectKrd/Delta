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
        'from',
        'state',
        'desc'
    ];

    public static function getAlarm($id) {
        return self::query()->where('id', '=', $id)->first();
    }

    public static function getFromUser($user) {
        return self::query()->where('from', '=', $user)->first();
    }

    public static function checkIsOpen($object) {
        if(self::query()->where('object_id', '=', $object)->where('state', '=', 'open')->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function openAlarm($alarm_id)
    {
        $api = new TelegramController();
        $alarm = self::getAlarm($alarm_id);
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $from_user = UnderSecurity::getUnderSecurityUser($alarm->from);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if (is_null($object->address)) {
                $text = "<b>ТРЕВОГА</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                    "Тип: " . $object->type . "\n".
                    "Описание: ".$alarm->desc;
                $api->response(
                    $api->withButtons(
                        $api->builder($text, $user->telegram_id),
                        [
                            ['text' => 'Отреагировать', 'command' => 'ack ' . $alarm_id]
                        ],
                    )
                );
            } else {
                $text = "<b>ТРЕВОГА</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                    "Адресс: " . $object->address . "\n" .
                    "Тип: " . $object->type."\n".
                    "Описание: ".$alarm->desc;
                $api->response(
                    $api->withButtons(
                        $api->builder($text, $user->telegram_id),
                        [
                            ['text' => 'Отреагировать', 'command' => 'ack ' . $alarm_id]
                        ],
                    )
                );
            }
        }
        $api->broadcast($text);
    }

    public static function closeAlarm($alarm_id)
    {
        $api = new TelegramController();
        $alarm = self::getAlarm($alarm_id);
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $from_user = UnderSecurity::getUnderSecurityUser($alarm->from);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if (is_null($object->address)) {
                $text = "<b>Отбой тревоги</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                    "Тип: " . $object->type . "\n".
                    "Описание: ".$alarm->desc;
                $api->response(
                    $api->builder($text, $user->telegram_id),
                );
            } else {
                $text = "<b>Отбой тревоги</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                    "Адресс: " . $object->address . "\n" .
                    "Тип: " . $object->type."\n".
                    "Описание: ".$alarm->desc;
                $api->response(
                    $api->builder($text, $user->telegram_id),
                );
            }
        }
        $api->broadcast($text);
    }

    /**
     * @param $alarm_id
     * @param $lat, Latitude
     * @param $lon, Longitude
     * @param $zoom
     * @return void
     */
    public static function sendLoc($alarm_id, $lat, $lon, $zoom = '16') {
        $api = new TelegramController();
        $alarm = self::getAlarm($alarm_id);
        $object_id = $alarm->object_id;
        $object = Objects::getObject($object_id);
        $from_user = UnderSecurity::getUnderSecurityUser($alarm->from);
        $link = "https://yandex.ru/maps/?pt=".$lon.",".$lat."&z=".$zoom."&l=map";
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            $text = "<b>Получены координаты тревоги</b> \n".
                "Номер обьекта: " . $object_id . "\n" .
                "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                "Локация: ". $link ." \n" .
                "Тип: " . $object->type . "\n".
                "Описание: ".$alarm->desc;
            $api->response(
                $api->builder($text, $user->telegram_id),
            );
        }
        $api->broadcast($text);
    }
}
