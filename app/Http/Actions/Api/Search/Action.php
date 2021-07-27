<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search;

use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Models\Section;
use Elasticsearch\Client;
use Illuminate\Http\Request;
use ScoutElastic\Facades\ElasticClient;

class Action
{
    public function __invoke(Request $request)
    {
        $sections = Section::all()->keyBy('id');

        /** @var Client $client */
        $client = ElasticClient::getFacadeRoot();

        $body = ['query' => ['query_string' => ['query' => $request->get('search')]]];
        $index = $sections->map(fn(Section $section) => $section->id . '_write')->join(',');
        $searchResults = collect($client->search(compact('body', 'index'))['hits']['hits'])->keyBy('_id');
        $modelResults = collect();

        foreach ($searchResults->groupBy('_index') as $index => $group) {
            $id = explode('_', $index)[0];
            $material = $sections->get($id)->class_name;


            $modelResults->push(
                ...$material::whereIn('id', $group->pluck('_id')->toArray())->get()
            );
        }

        $modelResults = $modelResults
            ->sortBy(fn(Material $material) => $searchResults->get($material->id)['_score'], SORT_REGULAR, true);

        return MaterialResource::collection($modelResults);
    }
}
