<?php

use App\Models\Section;
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


\Artisan::command('schema:drop', function () {
    /** @var \App\Services\TableBuilder $tableBuilder */

    DB::statement(\DB::raw("DROP SCHEMA pivots cascade"));
    DB::statement(\DB::raw("DROP SCHEMA sections cascade"));
    DB::statement(\DB::raw("CREATE SCHEMA sections"));
    DB::statement(\DB::raw("CREATE SCHEMA pivots"));
});

\Artisan::command('schema:build', function () {
    /** @var \App\Services\TableBuilder $tableBuilder */

    $tableBuilder = app(\App\Services\TableBuilder::class);

    foreach (Section::all() as $section) {
        $tableBuilder->create($section);
    }
});




\Artisan::command('schema:refresh', function () {
    /** @var \App\Services\TableBuilder $tableBuilder */


    DB::statement(\DB::raw("DROP SCHEMA IF EXISTS pivots cascade"));
    DB::statement(\DB::raw("DROP SCHEMA IF EXISTS sections cascade"));
    DB::statement(\DB::raw("CREATE SCHEMA IF NOT EXISTS sections"));
    DB::statement(\DB::raw("CREATE SCHEMA IF NOT EXISTS pivots"));

    $tableBuilder = app(\App\Services\TableBuilder::class);

    foreach (Section::all() as $section) {
        $tableBuilder->create($section);
    }
});

\Artisan::command('class:refresh', function () {
    /** @var \App\Services\MaterialClassManager $classBuilder */
    $classBuilder = app(\App\Services\MaterialClassManager::class);

    foreach (Section::all() as $section) {
        $classBuilder->remember($section);
    }
});

\Artisan::command('index:refresh', function () {
    foreach (Section::all() as $section) {
        app()->call([(new \App\Jobs\DropSectionIndex($section->id)), 'handle']);
        app()->call([(new \App\Jobs\CreateSectionIndex($section->id)), 'handle']);
        foreach ($section->class_name::all() as $material) {
            app()->call([new \App\Jobs\CreateMaterialDocument($section->class_name, $material->id), 'handle']);
        }
    }
});

