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
    $user->name = $this->ask('Имя пользователя');
    $user->password = $this->ask('Пароль пользователя');
    $user->super = $this->confirm('Суперпользователь?', false);
    $user->role = $user->super ? User::ROLE_ADMIN : $this->askWithCompletion('Роль пользователя', User::ROLES, 'user');
    $user->save();
})->purpose('Создать нового пользователя');

Artisan::command('elastic:clear', function () {
    foreach (config('scout_elastic.client.hosts') as $host) {
        (new \GuzzleHttp\Client())->delete(str_replace('9200:9200', '9200', env('SCOUT_ELASTIC_HOST') . ':9200/*'));
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

\Artisan::command('schema:columns', function () {
    /** @var \App\Services\TableBuilder $tableBuilder */

    $tableBuilder = app(\App\Services\TableBuilder::class);

    foreach (Section::all() as $section) {
        $tableBuilder->buildColumns($section);
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

\Artisan::command('index:reindex', function () {
    foreach (Section::withoutTrashed()->cursor() as $section) {
        app()->call([(new \App\Jobs\DropSectionIndex($section->id)), 'handle']);
        app()->call([(new \App\Jobs\CreateSectionIndex($section->id)), 'handle']);
        foreach ($section->class_name::withoutTrashed()->cursor() as $material) {
            app()->call([new \App\Jobs\CreateMaterialDocument($section->class_name, $material->id), 'handle']);
        }
    }
});


\Artisan::command('kb:fresh', function () {

    /** @var ClosureCommand $this */
    DB::statement(\DB::raw("DROP SCHEMA IF EXISTS pivots cascade"));
    DB::statement(\DB::raw("DROP SCHEMA IF EXISTS sections cascade"));
    DB::statement(\DB::raw("CREATE SCHEMA IF NOT EXISTS sections"));
    DB::statement(\DB::raw("CREATE SCHEMA IF NOT EXISTS pivots"));

    \Artisan::call('migrate:fresh', [], $this->getOutput());
    \Artisan::call('elastic:clear', [], $this->getOutput());
    \Artisan::call('db:seed', [], $this->getOutput());
});
