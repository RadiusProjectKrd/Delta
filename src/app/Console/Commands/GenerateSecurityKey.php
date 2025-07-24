<?php

namespace App\Console\Commands;

use app\Models\Security\Key;
use App\Models\Security\Objects;
use App\Models\Security\UnderSecurity;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class GenerateSecurityKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sec-key {user_id} {object_id} {desc}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создания ключа (id клиента) (id обьекта) (описание)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user_id = $this->argument('user_id');
        $object_id = $this->argument('object_id');
        $desc = $this->argument('desc');
        try {
            $user = UnderSecurity::getUnderSecurityUser($user_id);
        } catch (ModelNotFoundException $e) {
            echo 'Ошибка пользователь не найден';
        }

        try {
            $object = Objects::getObject($object_id);
        } catch (ModelNotFoundException $e) {
            echo 'Обьект не найден';
        }

        $random = Str::random();
        Key::query()->create([
            'user_id' => $user_id,
            'object_id' => $object_id,
            'key' => $random,
            'desc' => $desc
        ]);

        echo $random;
    }
}
