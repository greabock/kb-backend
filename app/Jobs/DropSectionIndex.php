<?php

declare(strict_types=1);

namespace App\Jobs;

use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DropSectionIndex implements ShouldQueue, ShouldBeUnique
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
        $client->indices()->delete(['index' => $this->sectionId]);
    }

    public function uniqueId(): string
    {
        return $this->sectionId;
    }
}
