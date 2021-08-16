<?php

use App\Models\User;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Support\Facades\Artisan;

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
    $user = new User();
    $user->login = $this->ask('Логин пользователя');
    $user->name = $this->ask('Имя пользователя');
    $user->password = $this->ask('Пароль пользователя');
    $user->role = $this->askWithCompletion('Роль пользователя', User::ROLES, 'user');
    $user->save();
})->purpose('Создать нового пользователя');

Artisan::command('elastic:clear', function () {
    foreach (config('scout_elastic.client.hosts') as $host) {
        (new \GuzzleHttp\Client())->delete(env('SCOUT_ELASTIC_HOST') . ':9200' . '/*');
        $this->info('Host ' . $host . ' cleared.');
    }
});

