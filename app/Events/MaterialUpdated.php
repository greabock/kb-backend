<?php

declare(strict_types=1);

namespace App\Events;

class MaterialUpdated
{
    public function __construct(
        public string $materialClass,
        public string $materialId
    )
    {
    }
}
