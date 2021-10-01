<?php

use App\Http\Actions\Api\Sections;
use OpenApi\Annotations as OA;

Route::prefix('sections')->name('sections')->group(function () {

    /**
     * @OA\Get(
     *     path="/sections",
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
     * @OA\Patch  (
     *     path="/sections",
     *     security={"apiKey":{}},
     *     tags={"Sections"},
     *     summary="Получение списка разделов",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UserMassUpdateRequest")),
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
    Route::patch('/')->name('.massUpdate')
        ->middleware('paid', 'user-role:admin')
        ->uses(Sections\MassUpdate\Action::class);

    /**
     * @OA\Post(
     *     path="/sections",
     *     security={"apiKey":{}},
     *     tags={"Sections"},
     *     summary="Создание раздела (admin)",
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
        ->middleware('paid', 'user-role:admin')
        ->uses(Sections\Create\Action::class);

    /**
     * @OA\Get (
     *     path="/sections/{section}",
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
     *     path="/sections/{sections}",
     *     security={"apiKey":{}},
     *     tags={"Sections"},
     *     summary="Обновление раздела (admin)",
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
        ->middleware('paid', 'user-role:admin')
        ->uses(Sections\Update\Action::class);

    /**
     * @OA\Delete  (
     *     path="/sections/{section}",
     *     security={"apiKey":{}},
     *     tags={"Sections"},
     *     summary="Удаление раздела (admin)",
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
        ->middleware('paid', 'user-role:admin')
        ->uses(Sections\Delete\Action::class);

    require __DIR__ . '/sections/materials.php';
});
