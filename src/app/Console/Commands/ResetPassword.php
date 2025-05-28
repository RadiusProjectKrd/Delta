<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-password {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сброс пароля';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::query()->where('id', '=', $this->argument('username'))->update(['password' => Hash::make($this->argument('password'))]);
    }
}
