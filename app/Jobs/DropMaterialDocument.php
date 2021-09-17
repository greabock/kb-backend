<?php

namespace App\Jobs;

use App\Models\Material;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DropMaterialDocument implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $materialClass,
        private string $materialId,
    )
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $esClient): void
    {
        /** @var Material $material */
        $material = $this->materialClass::withTrashed()->find($this->materialId);

        if($esClient->exists([
            'index' => $material->sectionId . '_write',
            'id' => $material->id,
        ])) {
            $esClient->delete([
                'index' => $material->sectionId . '_write',
                'id' => $material->id,
            ]);
        }
    }

    public function uniqueId(): string
    {
        return $this->materialId;
    }
}
