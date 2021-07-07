<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Actions\Auth;
use OpenApi\Annotations as OA;

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

Route::prefix('oauth')->group(function () {
    Route::prefix('azure')->group(function () {
        Route::get('redirect', Auth\Azure\Redirect\Action::class);
        Route::post('login', Auth\Azure\Login\Action::class);
    });
});

Route::post('login', Auth\Login\Action::class)->middleware(['guest']);
Route::post('logout', Auth\Logout\Action::class)->middleware(['auth:sanctum']);


/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="KB API",
 *     description="HTTP JSON API"
 * )
 * @OA\Server(
 *     url="/api"
 * )
 */


Route::prefix('api')->middleware('auth:sanctum')->group(function () {

    /**
     * @OA\Get(
     *     path="/user",
     *     security={"session":{}},
     *     description="Получение данных текущего пользователя",
     *     @OA\Response(
     *          response="200",
     *          description="Данные текущего пользователя",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });
});
