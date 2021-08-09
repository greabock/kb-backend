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
        ->uses(Files\Upload\Action::class);

    Route::get('/{file}')->name('.download')
        ->uses(Files\Download\Action::class);
});
