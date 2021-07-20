<?php

use App\Http\Actions\Api\Materials;
use OpenApi\Annotations as OA;

//sections/section/materials
Route::prefix('{section}/materials')->name('.materials')->group(function () {

    /**
     * @OA\Get(
     *     path="sections/{section}/materials",
     *     security={"apiKey":{}},
     *     tags={"Materials"},
     *     description="> Стоит обратить внимание, что для материала в списке, будут отображаться только те дополнительные поля,
    для которых установлен флаг `is_present_in_card: true`. Кроме того, хотя в примере и указаны ключи вида `additionalPropN`,
    на самом деле это всегда будет uuid свойства, для которого представлено значение. К сожалению, спецификация **OpenApi** не позволяет выразить это явным образом",
     *     summary="Получение постранично разбитого списка материалов секции",
     *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела материалов"),
     *     @OA\Response(
     *          response="200",
     *          description="Страница материалов",
     *          @OA\JsonContent(type="object",
     *             ref="#components/schemas/PageOfMaterialResources",
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::get('/')->name('.index')
        ->uses(Materials\Index\Action::class);

    /**
     * @OA\Post(
     *     path="sections/{section}/materials",
     *     security={"apiKey":{}},
     *     tags={"Materials"},
     *     summary="Создание материала",
     *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела материалов"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MaterialCreateRequest")),
     *     @OA\Response(
     *          response="201",
     *          description="Созданный материал",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/MaterialDetailedResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::post('/')->name('.create')
        ->uses(Materials\Create\Action::class);

    /**
     * @OA\Get(
     *     path="sections/{section}/materials/{material}",
     *     security={"apiKey":{}},
     *     tags={"Materials"},
     *     summary="Получение материала",
     *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела материалов"),
     *     @OA\Parameter(name="material", in="path", description="Идентификатор материала"),
     *     @OA\Response(
     *          response="200",
     *          description="Материал",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/MaterialDetailedResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::get('{material}')->name('.show')
        ->uses(Materials\Show\Action::class);

    /**
     * @OA\Patch(
     *     path="sections/{section}/materials/{material}",
     *     security={"apiKey":{}},
     *     tags={"Materials"},
     *     summary="Обновление материала",
     *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела материалов"),
     *     @OA\Parameter(name="material", in="path", description="Идентификатор обновляемого материала"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MaterialUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="Обновленный материал",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/MaterialDetailedResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::patch('{material}')->name('.update')
        ->uses(Materials\Update\Action::class);


    /**
     * @OA\Delete(
     *     path="sections/{section}/materials/{material}",
     *     security={"apiKey":{}},
     *     tags={"Materials"},
     *     summary="Удаление материала",
     *     @OA\Parameter(name="section", in="path", description="Идентификатор раздела материалов"),
     *     @OA\Parameter(name="material", in="path", description="Идентификатор удаляемого материала"),
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
    Route::delete('{material}')->name('.destroy')
        ->uses(Materials\Delete\Action::class);
});
