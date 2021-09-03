<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Section;
use App\Services\MaterialClassManager;

class SectionObserver
{
    public function __construct(
        private MaterialClassManager $modelBuilder,
    )
    {
    }

    public function updated(Section $section)
    {
        $this->modelBuilder->remember($section);
    }
}
