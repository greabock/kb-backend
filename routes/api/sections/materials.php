<?php

use App\Http\Actions\Api\Materials;

//sections/section/materials
Route::prefix('{section}/materials')->name('.materials')->group(function () {
    Route::get('/')->name('.index')
        ->uses(Materials\Index\Action::class);

    Route::post('/')->name('.create')
        ->uses(Materials\Create\Action::class);

    Route::patch('{material}')->name('.update')
        ->uses(Materials\Update\Action::class);

    Route::delete('{material}')->name('.destroy')
        ->uses(Materials\Delete\Action::class);
});
