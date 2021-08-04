<?php

use App\Http\Actions\Api\Search;

Route::get('search')->name('search')->uses(Search\Action::class);
Route::get('search/{section}')->name('search.filter')->uses(Search\Filter\Action::class);

