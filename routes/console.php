<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('user:create', function () {
    /** @var ClosureCommand $this */
    $user = new \App\Models\User();
    $user->login = $this->ask('Логин пользователя');
    $user->name = $this->ask('Имя пользователя');
    $user->password = \Illuminate\Support\Facades\Hash::make($this->ask('Пароль пользователя'));
    $user->role = $this->askWithCompletion('Роль пользователя', ['admin', 'moderator', 'user'], 'user');
    $user->save();

})->purpose('Создать нового пользователя');


Artisan::command('testo', function () {
    
});
