<?php

namespace App\Http\Controllers\Api\Security;

use App\Http\Controllers\Controller;
use App\Models\Security\Key;
use App\Models\Security\Objects;
use App\Models\Security\Alarm as AlarmModel;
use App\Models\Security\UnderSecurity;
use App\Models\Security\UserObjects;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class AlarmController extends Controller
{
    public function open($key) {
        Log::info('Trying to alarm');
        $api = new TelegramController();
        try {
            $object_id = Key::query()->where('key', '=', $key)->firstOrFail();
            $user = UnderSecurity::getUnderSecurityUser(Key::getByKey($key)->user_id);
            if(AlarmModel::checkIsOpen($object_id->object_id)) {
                return response()->json(['success' => false, 'error' => 'По данному обьекту уже вызвана тревога'], 409);
            } elseif(Objects::getObject($object_id->object_id)->state == 0) {
                return response()->json(['success' => false, 'error' => 'Обьект не под охраной'], 400);
            } else {
                $alarm = AlarmModel::query()->create([
                    'object_id' => $object_id->object_id,
                    'state' => 'open',
                    'from' => $user->id,
                    'desc' => Key::getByKey($key)->desc
                ]);
                AlarmModel::openAlarm($alarm->id);
                $api->response(
                    $api->withButtons(
                        $api->builder('Активирована тревога по обьекту ' . $object_id->object_id . '!', $user->telegram_id),
                        [
                            ['text' => 'Заркыть тревогу', 'command' => 'close ' . $alarm->id]
                        ]
                    )
                );
                return response()->json(['success' => true, 'message' => 'Активирована тревога по обьекту ' . $object_id->object_id . '!', $user->telegram_id]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Ключ не действительный'], 403);
        }
    }
}
