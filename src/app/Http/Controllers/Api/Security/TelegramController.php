<?php

namespace App\Http\Controllers\Api\Security;

use App\Http\Controllers\Controller;
use App\Models\Security\Ack;
use App\Models\Security\Alarm;
use App\Models\Security\Objects;
use App\Models\Security\UnderSecurity;
use App\Models\Security\UserObjects;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    private string $token;
    private string $broadcast_channel;
    private string $broadcast_thread;

    public function __construct()
    {
        $this->token = config('telegram.security.token');
        $this->broadcast_channel = config('telegram.security.broadcast_channel');
        $this->broadcast_thread = config('telegram.security.broadcast_thread');
    }

    public function broadcast($text): void
    {
        $data = $this->builder($text, $this->broadcast_channel);
        $data['reply_to_message_id'] = $this->broadcast_thread;
        $this->response($data);
    }

    public function handler(Request $request): void
    {
        Log::info('Telegram message', $request->all());
        $text = $request->input('message.text');
        $callback = $request->input('callback_query');

        if ($callback != null) {
            $chatId = $request->input('callback_query.from.id');
        } elseif ($text != null) {
            $chatId = $request->input('message.from.id');
        } elseif ($request->input('my_chat_member') != null) {
            Log::info('New Group Message');
        }

        $user = UnderSecurity::where('telegram_id', $chatId)->first();
        if ($user) {
            if ($callback != null) {
                Log::info('Callback Proccessing');
                $chatId = $request->input('callback_query.from.id');
                switch ($callback['data']) {
                    case 'menu':
                        $this->response(
                            $this->withButtons(
                                $this->builder("Меню", $chatId),
                                [
                                    ['text' => 'Аккаунт', 'command' => 'me'],
                                    ['text' => 'Обьекты', 'command' => 'objects'],
                                ]
                            )
                        );
                        break;

                    case 'me':
                        $this->response(
                            $this->withButtons(
                                $this->builder(
                                    "ID: " . $user->id . "\n" .
                                    "ФИО: " . $user->first_name . " " . $user->last_name . "\n",
                                    $chatId),
                                [
                                    ['text' => 'На главную', 'command' => 'menu'],
                                ]
                            ),
                        );
                        break;

                    case 'objects':
                        $objects = UserObjects::getAll($user->id);
                        if (count($objects) > 0) {
                            $this->response(
                                $this->builder('Ваши обьекты:', $chatId)
                            );

                            foreach ($objects as $user_object) {
                                $object = Objects::getObject($user_object->object_id);
                                if (is_null($object->address)) {
                                    $this->response(
                                        $this->withButtons(
                                            $this->builder(
                                                "<b>Номер обьекта:</b> " . $object->object_id . "\n" .
                                                "<b>Название:</b> " . $object->name . "\n" .
                                                "<b>Тип:</b> " . $object->type . "\n",
                                                $chatId),
                                            [
                                                ['text' => 'Отправить тревогу', 'command' => 'alarm ' . $object->object_id]
                                            ])
                                    );
                                } else {
                                    $this->response(
                                        $this->withButtons(
                                            $this->builder(
                                                "<b>Номер обьекта:</b> " . $object->object_id . "\n" .
                                                "<b>Название:</b> " . $object->name . "\n" .
                                                "<b>Адресс:</b> " . $object->address . "\n" .
                                                "<b>Тип:</b> " . $object->type . "\n",
                                                $chatId),
                                            [
                                                ['text' => 'Отправить тревогу', 'command' => 'alarm ' . $object->object_id]
                                            ])
                                    );
                                }
                            }
                        } else {
                            $this->response(
                                $this->builder('У вас нет обьектов', $chatId)
                            );
                        }
                        break;

                    default:
                        $data = explode(' ', $callback['data']);
                        Log::info(json_encode(['payload' => $data]));
                        if ($data[0] == 'alarm') {
                            Log::info('Trying to alarm');
                            try {
                                $object_id = UserObjects::getOne($user->id, explode(' ', $callback['data'])[1]);
                                if(Alarm::checkIsOpen($object_id->object_id)) {
                                    $this->response(
                                        $this->builder('По данному обьекту уже вызвана тревога', $chatId)
                                    );
                                } else {
                                    $alarm = Alarm::query()->create([
                                        'object_id' => $object_id->object_id,
                                        'state' => 'open',
                                        'from' => $user->id
                                    ]);
                                    Alarm::openAlarm($alarm->id);
                                    $this->response(
                                        $this->withButtons(
                                            $this->builder('Активирована тревога по обьекту ' . $object_id->object_id . '!', $chatId),
                                            [
                                                ['text' => 'Заркыть тревогу', 'command' => 'close ' . $alarm->id]
                                            ]
                                        )
                                    );
                                }
                            } catch (ModelNotFoundException $e) {
                                $this->response(
                                    $this->builder('Обьект не найден', $chatId)
                                );
                            }
                        } elseif ($data[0] == 'close') {
                            Log::info('Trying to close');
                            try {
                                $alarm = Alarm::query()->where('id', '=', $data[1])->firstOrFail();
                                if($alarm->state == 'close') {
                                    $this->response(
                                        $this->builder('Тревога с этим номером уже закрыта!', $chatId)
                                    );
                                } else {
                                    $object = Objects::getObject($alarm->object_id);
                                    Alarm::query()->where('id', '=', $alarm->id)->update([
                                        'state' => 'close'
                                    ]);
                                    Alarm::closeAlarm($alarm->id);
                                    $this->response(
                                        $this->builder('Тревога по обьекту ' . $object->object_id . ' закрыта', $chatId),
                                    );
                                }
                            } catch (ModelNotFoundException $e) {
                                $this->response(
                                    $this->builder('Тревога не найдена', $chatId)
                                );
                            }

                        } elseif ($data[0] == 'ack') {
                            Log::info('Trying to ack');
                            try {
                                $alarm = Alarm::query()->where('id', '=', $data[1])->firstOrFail();
                                if($alarm->state == 'close') {
                                    $this->response(
                                        $this->builder('Тревога с этим номером уже закрыта!', $chatId)
                                    );
                                } else {
                                    if (Ack::checkAlreayAcked($alarm->id, $user->id)) {
                                        $this->response(
                                            $this->builder('Вы уже реагировали на эту тревогу', $chatId)
                                        );
                                    } else {
                                        Ack::sendAck($alarm->id, $user->id);
                                        $this->response(
                                            $this->builder('Сигнал реагирования отправлен', $chatId),
                                        );
                                    }
                                }
                            } catch (ModelNotFoundException $e) {
                                $this->response(
                                    $this->builder('Тревога не найдена', $chatId)
                                );
                            }
                        } else {
                            $this->response(
                                $this->builder('Комманда не найдена', $chatId)
                            );
                        }
                }
            }

            if ($text != null) {
                Log::info('Message Proccessing');
                switch ($text) {
                    case '/start':
                        $this->response(
                            $this->withButtons(
                                $this->builder('Добро пожаловать', $chatId),
                                [
                                    ['text' => 'Главное меню', 'command' => 'menu'],
                                ]
                            )
                        );
                        break;
                }
            }
        } else {
            $this->response(
                $this->builder("Вы не авторизованы\nВаш ID: " . $chatId, $chatId)
            );
        }

    }

    public function response($data): void
    {
        Log::info(Http::post('https://api.telegram.org/bot' . $this->token . '/sendMessage', $data)->json());
    }

    public function builder(string $text, int $chatId): array
    {
        $data = [
            'text' => $text,
            'chat_id' => $chatId,
            'parse_mode' => 'HTML'
        ];
        return $data;
    }

    public function withButtons(array $data, array $buttons): array
    {
        $keyboard = [
            'inline_keyboard' => [],
        ];
        foreach ($buttons as $button) {
            $keyboard['inline_keyboard'][][] = ['text' => $button['text'], 'callback_data' => $button['command']];
        }
        $data['reply_markup'] = $keyboard;
        return $data;
    }

    public function sendAttachment(int $chatId, $attachment, string $name): void
    {
        Log::info('Attachment processing');
        Log::info(Http::post('https://api.telegram.org/bot' . $this->token . '/sendDocument', [
            'chat_id' => $chatId,
            'document' => $attachment,
            'caption' => $name,
            'parse_mode' => 'HTML'
        ]));
    }
}
