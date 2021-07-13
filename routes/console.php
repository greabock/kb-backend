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


Artisan::command('go', function () {

    /** @var \App\Models\Enum $enum */
    $enum = \App\Models\Enum::factory()->create();

    /** @var Greabock\Populator\Populator $populator */
    $populator = app(Greabock\Populator\Populator::class);
    $populator->populate(\App\Models\Section::class, [
        'title' => 'test section',
        'is_dictionary' => true,
        'is_navigation' => true,
        'sort_index' => 0,
        'fields' => [
            [
                'title' => 'test field',
                'required' => false,
                'use_in_card' => false,
                'sort_index' => 0,
                'type' => [
                    'name' => 'String',
                ],
            ],
            [
                'title' => 'test relations',
                'required' => false,
                'use_in_card' => false,
                'sort_index' => 0,
                'type' => [
                    'name' => 'File',
                ],
            ],
            [
                'title' => 'test relations',
                'required' => false,
                'use_in_card' => false,
                'sort_index' => 0,
                'type' => [
                    'name' => 'List',
                    'of' => [
                        'name' => 'Enum',
                        'of' => $enum->id
                    ]
                ],
            ],
        ],
    ]);

    $populator->flush();
});


Artisan::command('back', function () {
    \App\Models\Section::firstOrFail()->delete();
});
