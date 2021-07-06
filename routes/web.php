<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Actions\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('oauth/azure/redirect', Auth\Azure\Redirect\Action::class);
Route::get('test', function (){
    return 'hello';
});


Route::post('login', Auth\Login\Action::class)->middleware(['guest']);
Route::post('logout', Auth\Logout\Action::class)->middleware(['auth:sanctum']);

Route::prefix('api')->group(function (){
    Route::middleware('auth:sanctum')->group(function (){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
