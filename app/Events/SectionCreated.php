<?php

declare(strict_types=1);

namespace App\Events;

class SectionCreated
{
    public function __construct(
        public string $sectionId
    )
    {
    }
}
