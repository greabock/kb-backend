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
     *          description="Список групп",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array",
     *                @OA\Items(ref="#components/schemas/GroupResource")
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
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/GroupCreateRequest")),
     *     @OA\Response(
     *          response="201",
     *          description="Созданная группа",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/GroupResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::post('/')->uses(Groups\Create\Action::class)->name('.create');


    /**
     * @OA\Delete  (
     *     path="/groups/{group}",
     *     security={"apiKey":{}},
     *     tags={"Groups"},
     *     summary="Удаление группы",
     *     @OA\Parameter(name="group", in="path"),
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
    Route::delete('{group}')->uses(Groups\Delete\Action::class)->name('.delete');


    /**
     * @OA\Patch (
     *     path="/groups/{group}",
     *     security={"apiKey":{}},
     *     tags={"Groups"},
     *     summary="Обновление группы",
     *     @OA\Parameter(name="group", in="path"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/GroupCreateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="Обновленная группа",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/GroupResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::patch('{group}')->uses(Groups\Update\Action::class)->name('.update');
});
