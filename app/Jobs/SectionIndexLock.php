<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Section;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SectionIndexLock implements ShouldQueue, ShouldBeUnique
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
    public function handle(): void
    {
        $section = Section::find($this->sectionId);
        $section->indexing = true;
        $section->save();
    }

    public function uniqueId(): string
    {
        return $this->sectionId;
    }
}
