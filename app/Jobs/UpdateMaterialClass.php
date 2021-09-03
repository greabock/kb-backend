<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Section;
use App\Services\MaterialClassManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateMaterialClass implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable;

    public function __construct(
        private $sectionId
    )
    {
    }

    public function handle(MaterialClassManager $manager)
    {
        $manager->remember(Section::find($this->sectionId));
    }

    public function uniqueId()
    {
        return $this->sectionId;
    }
}
