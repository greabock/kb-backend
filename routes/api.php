<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/enums.php';
    require __DIR__ . '/api/sections.php';
    require __DIR__ . '/api/users.php';
    require __DIR__ . '/api/search.php';
});
