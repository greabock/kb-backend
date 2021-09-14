<?php

use App\Http\Actions\Api\Groups;


Route::prefix('groups')->name('groups')->group(function () {

    /**
     * @OA\Get(
     *     path="/groups",
     *     security={"apiKey":{}},
     *     tags={"Groups"},
     *     summary="Получение списка групп",
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
    Route::get('/')->uses(Groups\Index\Action::class)->name('.index');


    /**
     * @OA\Post(
     *     path="/groups",
     *     security={"apiKey":{}},
     *     tags={"Groups"},
     *     summary="Создание группы",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EnumCreateRequest")),
     *     @OA\Response(
     *          response="201",
     *          description="Созданная группа",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/EnumDetailedResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::post('/')->uses(Groups\Index\Action::class)->name('.create');



    /**
     * @OA\Get (
     *     path="/groups/{group}",
     *     security={"apiKey":{}},
     *     tags={"Groups"},
     *     summary="Получение группы с пользователями",
     *     @OA\Parameter(name="enum", in="path"),
     *     @OA\Response(
     *          response="200",
     *          description="Конкретная группа со списком пользователей",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/EnumDetailedResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::get('{group}')->uses(Groups\Index\Action::class)->name('.show');

    Route::delete('{group}')->uses(Groups\Index\Action::class)->name('.delete');



    /**
     * @OA\Patch (
     *     path="/groups/{group}",
     *     security={"apiKey":{}},
     *     tags={"Groups"},
     *     summary="Обновление группы",
     *     @OA\Parameter(name="enum", in="path"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EnumUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="Обновленная группа",
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
    Route::patch('{group}')->uses(Groups\Index\Action::class)->name('.update');
});
