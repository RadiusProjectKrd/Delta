<?php

namespace App\Models\Security;

use App\Http\Controllers\Api\Security\TelegramController;
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

    public static function changeState($object_id, $state, $by) {
        $api = new TelegramController();
        $object = Objects::getObject($object_id);
        $from_user = UnderSecurity::getUnderSecurityUser($by);
        foreach (UnderSecurity::getUnderSecurityUsers() as $user) {
            if ($state == 0) {
                $text = "<b>Обьект ".$object_id." снят с охраны</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                    "Тип: " . $object->type . "\n".
                $api->response(
                        $api->builder($text, $user->telegram_id),
                );
            } elseif($state == 1) {
                $text = "<b>Обьект ".$object_id." поставлен на охрану</b> \n" .
                    "Номер обьекта: " . $object_id . "\n" .
                    "Пользователь: " . $from_user->first_name . " " . $from_user->last_name . "\n" .
                    "Тип: " . $object->type . "\n".
                    $api->response(
                        $api->builder($text, $user->telegram_id),
                    );
            }
        }
        $api->broadcast($text);
    }
}
