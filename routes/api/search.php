<?php

use App\Http\Actions\Api\Search;
use OpenApi\Annotations as OA;

/**
 * @OA\Get (
 *     path="/search",
 *     security={"apiKey":{}},
 *     tags={"Search"},
 *     summary="Поиск по файлам и материалам всех разделов",
 *     @OA\Parameter(name="search", in="query"),
 *     @OA\Parameter(name="sort", in="query",
 *         @OA\Schema(type="object",
 *              @OA\Property(property="direction", type="string", enum={"asc", "desc"} ),
 *              @OA\Property(property="field", type="string", enum={"created_at", "name"} )
 *         )
 *     ),
 *     @OA\Parameter(name="extensions", in="query",
 *         @OA\Schema(type="array",
 *            @OA\Items(type="string", example="docx")
 *         )
 *     ),
 *     @OA\Response(
 *          response="200",
 *          description="Списки материалов и файлов",
 *          @OA\JsonContent(type="object",
 *             @OA\Property(property="data", ref="#components/schemas/SearchResultResource")
 *          )
 *     ),
 *     @OA\Response(
 *          response="401",
 *          description="Неаутентифицирован",
 *     )
 * )
 */
Route::get('search')->name('search')->uses(Search\Action::class);


Route::get('search/{section}')->name('search.filter')->uses(Search\Filter\Action::class);

