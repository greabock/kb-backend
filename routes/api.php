<?php

use Illuminate\Support\Facades\Route;
use App\Http\Actions\Api\Auth;
use App\Http\Actions\Api\Sections;
use App\Http\Actions\Api\Enums;
use OpenApi\Annotations as OA;

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

Route::prefix('auth')->name('auth')->group(function () {

    /**
     * @OA\Post(
     *     path="auth/login",
     *     security={},
     *     tags={"Auth"},
     *     summary="Получение токена по логину и паролю",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/LoginRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="Токен аутентификации",
     *          @OA\JsonContent(ref="#/components/schemas/ApiTokenResource")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     ),
     * )
     */
    Route::post('login')->name('.login')
        ->uses(Auth\Login\Action::class);

    Route::prefix('azure')->name('.azure')->group(function () {

        /**
         * @OA\Get(
         *     path="auth/azure/redirect",
         *     security={},
         *     tags={"Auth"},
         *     summary="Получение редиректа для перенаправления",
         *     @OA\Response(
         *          response="200",
         *          description="Адрес перенаправления для внешней утентификации",
         *          @OA\JsonContent(ref="#/components/schemas/RedirectResource")
         *     ),
         * )
         */
        Route::get('redirect')
            ->name('.redirect')
            ->uses(Auth\Azure\Redirect\Action::class);

        /**
         * @OA\Post(
         *     path="auth/azure/login",
         *     security={},
         *     description="Нужно пробросить в тело все параметры с query string редиректа 'как есть'",
         *     summary="Получение токена после перенаправления",
         *     tags={"Auth"},
         *     @OA\Response(
         *          response="200",
         *          description="Токен аутентификации",
         *          @OA\JsonContent(ref="#/components/schemas/ApiTokenResource")
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     ),
         * )
         */
        Route::post('login')
            ->name('.login')
            ->uses(Auth\Azure\Login\Action::class);
    });

    /**
     * @OA\Get(
     *     path="auth/me",
     *     security={"apiKey":{}},
     *     tags={"Auth"},
     *     summary="Получение данных текущего пользователя",
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
    Route::get('me')->name('.me')
        ->middleware('auth:sanctum')
        ->uses(Auth\Me\Action::class);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('sections')->name('sections')->group(function () {

        Route::get('/')->name('.index')
            ->uses(Sections\Index\Action::class);

        Route::post('/')->name('.create')
            ->uses(Sections\Create\Action::class);

        Route::get('{section}')->name('.show')
            ->uses(Sections\Show\Action::class);

        Route::patch('{section}')->name('.update')
            ->uses(Sections\Create\Action::class);

        Route::delete('{section}')->name('.destroy')
            ->uses(Sections\Destroy\Action::class);
    });

    Route::prefix('enums')->name('enums')->group(function () {

        Route::get('/')->name('.index')
            ->uses(Enums\Index\Action::class);

        Route::post('/')->name('.create')
            ->uses(Enums\Create\Action::class);

        Route::get('{enum}')->name('.show')
            ->uses(Enums\Show\Action::class);

        Route::patch('{enum}')->name('.update')
            ->uses(Enums\Update\Action::class);

        Route::delete('{enum}')->name('.destroy')
            ->uses(Enums\Destroy\Action::class);
    });
});



