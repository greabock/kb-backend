<?php

use App\Http\Actions\Api\Search;

Route::get('search')->name('search')->uses(Search\Action::class);

