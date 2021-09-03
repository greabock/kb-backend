<?php

declare(strict_types=1);

namespace App\Events;

class SectionDeleted
{
    public function __construct(
        public string $sectionId
    )
    {
    }
}
