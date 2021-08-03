<?php

use App\Http\Actions\Api\Files;

Route::prefix('files')->name('files')->group(function () {

    Route::post('/')->name('.upload')
        ->uses(Files\Upload\Action::class);

    Route::get('/{file}')->name('.content')
        ->uses(Files\Upload\Action::class);
});
