<?php

use App\Http\Actions\Api\Users;

Route::prefix('users')->name('users')->middleware('user-role:admin')->group(function () {

    /**
     * @OA\Get(
     *     path="/users",
     *     security={"apiKey":{}},
     *     tags={"Users"},
     *     summary="Получение списка пользователей (admin)",
     *     @OA\Response(
     *          response="200",
     *          description="Список пользователей",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array",
     *                @OA\Items(ref="#components/schemas/UserResource")
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     ),
     *     @OA\Response(
     *          response="403",
     *          description="Неавторизован",
     *     )
     * )
     */
    Route::get('/')->name('.index')
        ->middleware('user-role:admin')
        ->uses(Users\Index\Action::class);

    /**
     * @OA\Post(
     *     path="/users",
     *     security={"apiKey":{}},
     *     tags={"Users"},
     *     summary="Создание пользователя (admin)",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UserCreateRequest")),
     *     @OA\Response(
     *          response="201",
     *          description="Созданный пользователь",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/UserResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     ),
     *     @OA\Response(
     *          response="403",
     *          description="Неавторизован",
     *     )
     * )
     */
    Route::post('/')->name('.create')
        ->middleware('user-role:admin')
        ->uses(Users\Create\Action::class);

    /**
     * @OA\Patch (
     *     path="/users/{user}",
     *     security={"apiKey":{}},
     *     tags={"Users"},
     *     summary="Обновление пользователя (admin)",
     *     @OA\Parameter(name="user", in="path", description="Идентификатор пользователя"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="Обновленный пользователь",
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
    Route::patch('{user}')->uses(Users\Update\Action::class)
        ->middleware('user-role:admin')
        ->name('.update');

    /**
     * @OA\Delete  (
     *     path="/users/{user}",
     *     security={"apiKey":{}},
     *     tags={"Users"},
     *     summary="Удаление пользователя (admin)",
     *     @OA\Parameter(name="user", in="path", description="Идентификатор пользователя"),
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
    Route::delete('{user}')->name('.delete')
        ->middleware('user-role:admin')
        ->uses(Users\Delete\Action::class);
});
