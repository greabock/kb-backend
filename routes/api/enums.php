<?php

use App\Http\Actions\Api\Enums;
use OpenApi\Annotations as OA;

Route::prefix('enums')->name('enums')->group(function () {

    /**
     * @OA\Get(
     *     path="/enums",
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
     *     path="/enums",
     *     security={"apiKey":{}},
     *     tags={"Enums"},
     *     summary="Создание перечисления",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EnumCreateRequest")),
     *     @OA\Response(
     *          response="201",
     *          description="Созданное перечисление",
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
    Route::post('/')->name('.create')
        ->uses(Enums\Create\Action::class);

    /**
     * @OA\Get (
     *     path="/enums/{enum}",
     *     security={"apiKey":{}},
     *     tags={"Enums"},
     *     summary="Получение перечисления с его значениями",
     *     @OA\Parameter(name="enum", in="path"),
     *     @OA\Response(
     *          response="200",
     *          description="Конкретное перечисление с его значениями",
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
    Route::get('{enum}')->name('.show')
        ->uses(Enums\Show\Action::class);

    /**
     * @OA\Patch (
     *     path="/enums/{enum}",
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
     *     path="/enums/{enum}",
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
