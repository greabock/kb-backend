<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/enums.php';
    require __DIR__ . '/api/sections.php';
    require __DIR__ . '/api/users.php';
    require __DIR__ . '/api/search.php';
    require __DIR__ . '/api/files.php';
});


if (config('app.debug')) {
    Route::get('refresh/{type}', function (string $type) {
        switch ($type) {
            case 'index':
                Artisan::call('index:refresh');
                break;
            case 'class':
                Artisan::call('class:refresh');
                break;
            case 'schema':
                Artisan::call('schema:refresh');
                break;
            default:
                return;
        }
    });

    Route::get('refresh/{type}', function (string $type) {
        switch ($type) {
            case 'index':
                Artisan::call('index:refresh');
                break;
            case 'class':
                Artisan::call('class:refresh');
                break;
            case 'schema':
                Artisan::call('schema:refresh');
                break;
            default:
                return;
        }
    });
}
