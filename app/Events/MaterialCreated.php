<?php

declare(strict_types=1);

namespace App\Events;

class MaterialCreated
{
    public function __construct(
        public string $materialClass,
        public string $materialId
    )
    {
    }
}
