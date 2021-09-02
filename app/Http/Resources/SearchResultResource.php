<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="SearchResultResource",
 *      @OA\Property(property="materials", type="array",
 *          @OA\Items(ref="#/components/schemas/MaterialSearchResult")
 *      ),
 *      @OA\Property(property="files", type="array",
 *          @OA\Items(ref="#/components/schemas/FileSearchResult")
 *      ),
 * )
 * @OA\Schema(schema="FileSearchResult",
 *      @OA\Property(property="section", type="object",
 *          @OA\Property(property="id", type="string", format="uuid", example="ddb0b1e7-89e1-318b-ad0e-4dc7e2829db2")
 *      ),
 *      @OA\Property(property="material", type="object",
 *          @OA\Property(property="id", type="string", format="uuid", example="ddb0b1e7-89e1-318b-ad0e-4dc7e2829db2"),
 *          @OA\Property(property="name", type="string", format="uuid", example="Имя материала"),
 *      ),
 *      @OA\Property(property="highlight", type="object",
 *          @OA\Property(property="content", type="array",
 *              @OA\Items(type="string", example="Благодаря <em>удобному</em> для масштабирования характеру PHP и встроенной"),
 *          ),
 *          @OA\Property(property="name", type="array",
 *              @OA\Items(type="string", example="Имя <em>удобного</em> файла"),
 *          ),
 *      ),
 * )
 * @OA\Schema(schema="MaterialSearchResult",
 *      @OA\Property(property="section", type="object",
 *          @OA\Property(property="id", type="string", format="uuid", example="ddb0b1e7-89e1-318b-ad0e-4dc7e2829db2")
 *      ),
 *      @OA\Property(property="material", type="object",
 *          @OA\Property(property="id", type="string", format="uuid", example="ddb0b1e7-89e1-318b-ad0e-4dc7e2829db2"),
 *          @OA\Property(property="name", type="string", format="uuid", example="Имя материала"),
 *      ),
 *      @OA\Property(property="highlight", type="object",
 *          @OA\Property(property="name", type="array",
 *              @OA\Items(type="string", example="Название <em>удобного</em> материала"),
 *          ),
 *          @OA\AdditionalProperties(type="array",
 *               @OA\Items(type="string", example="Благодаря <em>удобному</em> для масштабирования характеру PHP и встроенной")
 *          )
 *      )
 * )
 */
class SearchResultResource extends JsonResource
{
}
