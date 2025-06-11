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

    public function webhook(): void
    {
        Log::info(Http::get('https://api.telegram.org/bot' . $this->token . '/setWebhook', [
            'url' => 'imperator.ultimatex.tech/api/bot/security'
        ]));
    }

    public function broadcast(Request $request): void
    {
        $data = $this->builder($request->input('text'), $this->broadcast_channel);
        $data['reply_to_message_id'] = $this->broadcast_thread;
        $this->response($data);
    }

    public function test_broadcast($text): void
    {
        $data = $this->builder($text, $this->broadcast_channel);
        $data['reply_to_message_id'] = $this->broadcast_thread;
        $this->response($data);
    }

    public function test_message($text, $chatId)
    {
        $this->response(
            $this->builder($text, $chatId)
        );
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
                                    "Имя: " . $user->name . "\n",
                                    $chatId),
                                [
                                    ['text' => 'На главную', 'command' => 'menu'],
                                ]
                            ),
                        );
                        break;

                    case 'objects':
                        $objects = UserObjects::getAll($user);
                        if (count($objects) > 0) {
                            $this->response(
                                $this->builder('Ваши документы:', $chatId)
                            );

                            foreach ($objects as $object) {
                                if (is_null($object->address)) {
                                    $this->response(
                                        $this->builder(
                                            "<b>Номер обьекта:</b> " . $object->object_id . "\n" .
                                            "<b>Название:</b> " . $object->name . "\n" .
                                            "<b>Тип:</b> " . $object->type . "\n",
                                            $chatId)
                                    );
                                } else {
                                    $this->response(
                                        $this->builder(
                                            "<b>Номер обьекта:</b> " . $object->object_id . "\n" .
                                            "<b>Название:</b> " . $object->name . "\n" .
                                            "<b>Адресс:</b> " . $object->address . "\n" .
                                            "<b>Тип:</b> " . $object->type . "\n",
                                            $chatId)
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
                        if (strpos('alarm ', $text)) {
                            try {
                                $object_id = UserObjects::getOne($user, explode(' ', $text)[1]);
                                $object = Objects::getObject($object_id);
                                $alarm = Alarm::query()->create([
                                    'object_id' => $object_id,
                                    'state' => 'open'
                                ]);
                                Alarm::openAlarm($alarm->id);
                                $this->response(
                                    $this->withButtons(
                                        $this->builder('Активирована тревога по обьекту ' . $object_id . '!', $chatId),
                                        [
                                            ['text' => 'Заркыть тревогу', 'command' => 'close ' . $alarm->id]
                                        ]
                                    )
                                );
                            } catch (ModelNotFoundException $e) {
                                $this->response(
                                    $this->builder('Обьект не найден', $chatId)
                                );
                            }
                        } elseif (strpos('close ', $text)) {
                            try {
                                $alarm = Alarm::query()->where('id', '=', explode(' ', $text)[1])->firstOrFail();
                                $object = Objects::getObject($alarm->object_id);
                                Alarm::query()->where('alarm_id', '=', $alarm->id)->update([
                                    'state' => 'close'
                                ]);
                                Alarm::closeAlarm($alarm->alarm_id);
                                $this->response(
                                    $this->builder('Тревога по обьекту ' . $object->id . ' закрыта', $chatId),
                                );
                            } catch (ModelNotFoundException $e) {
                                $this->response(
                                    $this->builder('Тревога не найдена', $chatId)
                                );
                            }

                        } elseif (strpos('ack ', $text)) {
                            try {
                                $alarm = Alarm::query()->where('id', '=', explode(' ', $text)[1])->firstOrFail();
                                $object = Objects::getObject($alarm->object_id);
                                Ack::sendAck($alarm->alarm_id, $user);
                                $this->response(
                                    $this->builder('Сигнал реагирования отправлен', $chatId),
                                );
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
