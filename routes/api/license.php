<?php

use App\Http\Actions\Api\License;
use OpenApi\Annotations as OA;

Route::prefix('license')->name('license')->group(function () {

    /**
     * @OA\Get(
     *     path="/license",
     *     security={"apiKey":{}},
     *     tags={"License"},
     *     summary="Получение информации о лицензии",
     *     @OA\Response(
     *          response="200",
     *          description="Информация о лицензии",
     *          @OA\JsonContent(type="object",
     *             @OA\Property(property="expires_at", type="date", example="2021-12-31")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::get('/', License\Index\Action::class)->name('.index');

    /**
     * @OA\Post(
     *     path="/license",
     *     security={"apiKey":{}},
     *     tags={"License"},
     *     summary="Обновление лицензии",
     *     @OA\RequestBody(
     *          @OA\Schema(type="object",
     *             @OA\Property(property="key", type="string", example="TVZRSv+U534IRXwQKcD1og==Nmw2QkZiZnJ1VHpEY2xwcmI5ZnM3ZThXNU81VjhUVm1haDBwcDk2Q2NXdCtFOFdSZHIzSTAzUUgrK3hSVEV6bA==")
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Неаутентифицирован",
     *     )
     * )
     */
    Route::post('/', License\Update\Action::class)->name('.update')->middleware('user-role:admin');
});
