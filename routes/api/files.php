<?php

use App\Http\Actions\Api\Files;
use OpenApi\Annotations as OA;


Route::prefix('files')->name('files')->group(function () {

    /**
     * @OA\Post(
     *     path="/files",
     *     security={},
     *     tags={"Files"},
     *     summary="Загрузка файла",
     *     @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(type="object", required={"files"},
     *              @OA\Property(property="files", type="array",
     *                  @OA\Items(type="string", format="binary")
     *              )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/FileResource")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     ),
     * )
     */
    Route::post('/')->name('.upload')
        ->middleware('paid')
        ->uses(Files\Upload\Action::class);

    Route::get('/{file}')->name('.download')
        ->uses(Files\Download\Action::class)
        ->middleware('signed')
        ->withoutMiddleware(['auth:sanctum']);

    /**
     * @OA\Get (
     *     path="/files/{file}/sign",
     *     security={"apiKey":{}},
     *     tags={"Files"},
     *     summary="Получение подписанной ссылки на файл",
     *     @OA\Parameter(name="file", in="path"),
     *     @OA\Response(
     *          response="200",
     *          description="Подписанная ссылка",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="string", description="Подписанная ссылка")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::get('/{file}/sign')->name('.sign')
        ->uses(Files\Sign\Action::class);

    /**
     * @OA\Patch (
     *     path="/files/{file}",
     *     security={"apiKey":{}},
     *     tags={"Files"},
     *     summary="Обновление файла",
     *     @OA\Parameter(name="file", in="path"),
     *     @OA\RequestBody(@OA\Schema(type="object", required={"name"},
     *          @OA\Property(property="name", type="string")
     *     )),
     *     @OA\Response(
     *          response="200",
     *          description="Обновленный файл",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#components/schemas/FileResource")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::patch('/{file}')->name('.update')
        ->uses(Files\Update\Action::class);
});
