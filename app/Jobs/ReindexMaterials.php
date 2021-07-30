<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Section;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReindexMaterials implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $sectionId
    )
    {
    }

    /**
     * Execute the job.
     *
     * @param Dispatcher $jobs
     * @return void
     */
    public function handle(Dispatcher $jobs): void
    {
        $section = Section::find($this->sectionId);

        foreach ($section->class_name::cursor() as $material) {
            $jobs->dispatch(new CreateMaterialDocument(
                $section->class_name,
                $material->id,
            ));
        }
    }

    public function uniqueId(): string
    {
        return $this->sectionId;
    }
}
