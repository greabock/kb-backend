<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="KB API",
 *     description="HTTP JSON API"
 * )
 * @OA\Server(
 *     url="/api"
 * )
 *
 * @OA\Schema(schema="PaginationMeta", type="object",
 *     @OA\Property(property="current_page", type="integer"),
 *     @OA\Property(property="from", type="integer"),
 *     @OA\Property(property="last_page", type="integer"),
 *     @OA\Property(property="per_page", type="integer"),
 *     @OA\Property(property="to", type="integer"),
 *     @OA\Property(property="total", type="integer"),
 *     @OA\Property(property="path", type="string"),
 * )
 *
 */
