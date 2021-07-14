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

        /**
         * @OA\Get(
         *     path="sections",
         *     security={"apiKey":{}},
         *     tags={"Sections"},
         *     summary="Получение списка разделов",
         *     @OA\Response(
         *          response="200",
         *          description="Список разделов",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", type="array",
         *                @OA\Items(ref="#components/schemas/SectionResource")
         *             )
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::get('/')->name('.index')
            ->uses(Sections\Index\Action::class);

        /**
         * @OA\Post(
         *     path="sections",
         *     security={"apiKey":{}},
         *     tags={"Sections"},
         *     summary="Создание раздела",
         *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/SectionCreateRequest")),
         *     @OA\Response(
         *          response="201",
         *          description="Созданный раздел",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", ref="#components/schemas/SectionWithFieldsResource")
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::post('/')->name('.create')
            ->uses(Sections\Create\Action::class);

        /**
         * @OA\Get (
         *     path="sections/{section}",
         *     security={"apiKey":{}},
         *     tags={"Sections"},
         *     summary="Получение раздела с его полями",
         *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела"),
         *     @OA\Response(
         *          response="200",
         *          description="Раздел с его значениями",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", ref="#components/schemas/SectionWithFieldsResource")
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::get('{section}')->name('.show')
            ->uses(Sections\Show\Action::class);

        /**
         * @OA\Patch (
         *     path="sections/{sections}",
         *     security={"apiKey":{}},
         *     tags={"Enums"},
         *     summary="Обновление перечисления",
         *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела"),
         *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/SectionUpdateRequest")),
         *     @OA\Response(
         *          response="200",
         *          description="Обновленный раздел",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", ref="#components/schemas/SectionWithFieldsResource")
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::patch('{section}')->name('.update')
            ->uses(Sections\Update\Action::class);

        /**
         * @OA\Delete  (
         *     path="sections/{section}",
         *     security={"apiKey":{}},
         *     tags={"Sections"},
         *     summary="Удаление раздела",
         *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела"),
         *     @OA\Response(
         *          response="204",
         *          description="Удалено",
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::delete('{section}')->name('.destroy')
            ->uses(Sections\Destroy\Action::class);
    });


    Route::prefix('enums')->name('enums')->group(function () {

        /**
         * @OA\Get(
         *     path="enums",
         *     security={"apiKey":{}},
         *     tags={"Enums"},
         *     summary="Получение списка перечислений",
         *     @OA\Response(
         *          response="200",
         *          description="Список перечислений",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", type="array",
         *                @OA\Items(ref="#components/schemas/EnumResource")
         *             )
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::get('/')->name('.index')
            ->uses(Enums\Index\Action::class);

        /**
         * @OA\Post(
         *     path="enums",
         *     security={"apiKey":{}},
         *     tags={"Enums"},
         *     summary="Создание перечисления",
         *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EnumCreateRequest")),
         *     @OA\Response(
         *          response="201",
         *          description="Созданное перечисление",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", ref="#components/schemas/EnumWithValuesResource")
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::post('/')->name('.create')
            ->uses(Enums\Create\Action::class);

        /**
         * @OA\Get (
         *     path="enums/{enum}",
         *     security={"apiKey":{}},
         *     tags={"Enums"},
         *     summary="Получение перечисления с его значениями",
         *     @OA\Parameter(name="enum", in="path"),
         *     @OA\Response(
         *          response="200",
         *          description="Конкретное перечисление с его значениями",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", ref="#components/schemas/EnumWithValuesResource")
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::get('{enum}')->name('.show')
            ->uses(Enums\Show\Action::class);

        /**
         * @OA\Patch (
         *     path="enums/{enum}",
         *     security={"apiKey":{}},
         *     tags={"Enums"},
         *     summary="Обновление перечисления",
         *     @OA\Parameter(name="enum", in="path"),
         *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EnumUpdateRequest")),
         *     @OA\Response(
         *          response="200",
         *          description="Обновленное перечисление",
         *          @OA\JsonContent(type="object",
         *             @OA\Property(property="data", ref="#components/schemas/EnumWithValuesResource")
         *          )
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::patch('{enum}')->name('.update')
            ->uses(Enums\Update\Action::class);

        /**
         * @OA\Delete  (
         *     path="enums/{enum}",
         *     security={"apiKey":{}},
         *     tags={"Enums"},
         *     summary="Удаление перечисления",
         *     @OA\Parameter(name="enum", in="path"),
         *     @OA\Response(
         *          response="204",
         *          description="Удалено",
         *     ),
         *     @OA\Response(
         *          response="401",
         *          description="Неаутентифицирован",
         *     )
         * )
         */
        Route::delete('{enum}')->name('.destroy')
            ->uses(Enums\Destroy\Action::class);
    });
});



