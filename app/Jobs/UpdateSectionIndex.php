<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Section;
use App\Services\Search\MaterialIndexConfigurator;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use ScoutElastic\Payloads\IndexPayload;

class UpdateSectionIndex implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $sectionId,
        private array $fields,
    )
    {
    }

    /**
     * Execute the job.
     * @param Client $client
     * @return void
     */
    public function handle(Client $client): void
    {
        if ($section = Section::find($this->sectionId)) {
            $client->indices()->putMapping([
                'index' => $section->id,
                'body' => \Arr::only($section->getMaterialMappings(), $this->fields),
            ]);
        }

        $section->indexing = false;
        $section->save();
    }

    public function uniqueId(): string
    {
        return $this->sectionId;
    }
}
