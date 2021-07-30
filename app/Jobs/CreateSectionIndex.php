<?php

namespace App\Jobs;

use App\Models\Section;
use App\Services\Search\MaterialIndexConfigurator;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use ScoutElastic\Payloads\IndexPayload;

class CreateSectionIndex implements ShouldQueue, ShouldBeUnique
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
     * @return void
     */
    public function handle(Client $client)
    {
        if ($section = Section::find($this->sectionId)) {
            $configurator = new MaterialIndexConfigurator($section->class_name);

            $client->indices()->create(
                (new IndexPayload($configurator))
                    ->setIfNotEmpty('body.settings', $configurator->getSettings())
                    ->setIfNotEmpty('body.mappings', $section->getMaterialMappings())
                    ->get()
            );

            $client->indices()
                ->putAlias(
                    (new IndexPayload($configurator))
                        ->set('name', $configurator->getWriteAlias())->get()
                );
        }

        $section->indexing = false;
        $section->save();
    }

    public function uniqueId(): string
    {
        return $this->sectionId;
    }
}
