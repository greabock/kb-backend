<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Section;
use Elasticsearch\Client;
use Illuminate\Support\Collection;
use Str;

class Search
{
    public function __construct(private Client $client)
    {
    }

    public function searchFiles(
        ?string $queryString,
        array $extensions,
        array $sort,
        Section\Field\Collection $fields,
        string $index,
    ): Collection
    {
        if ($fields->isEmpty()) {
            return collect();
        }

        $body = [
            'query' => ['bool' => ['should' => []]],
            '_source' => ['includes' => ['id', 'name']],
        ];

        foreach ($fields as $field) {

            $extensionMatches = [];

            foreach ($extensions as $extension) {
                $extensionMatches[] = ['term' => [$field->id . '.extension' => $extension]];
            }

            $must = [['bool' => ['should' => $extensionMatches]]];

            if (!empty($queryString)) {
                $must[] = ['query_string' => [
                    'query' => $queryString,
                    'fields' => [$field->id . '.name', $field->id . '.content'],
                ]];
            }

            $body['query']['bool']['should'][] = ['nested' => [
                'path' => $field->id,
                'query' => ['bool' => ['must' => $must]],
                'inner_hits' => [
                    '_source' => [$field->id . '.id', $field->id . '.name', $field->id . '.extension', $field->id . '.created_at'],
                    'highlight' => ['fields' => [$field->id . '.name' => (object)[], $field->id . '.content' => (object)[]]],
                    'sort' => [[$field->id . '.' . $sort['field'] => $sort['direction']]],
                ],
            ]];

        }

        $response = $this->client->search(compact('body', 'index'));

        $files = [];

        foreach ($response['hits']['hits'] as $hit) {
            foreach ($hit['inner_hits'] as $fieldId => ['hits' => ['hits' => $nestedHits]]) {
                foreach ($nestedHits as $nestedHit) {
                    $highlights = [
                        'content' => [],
                        'name' => [],
                    ];

                    if (isset($nestedHit['highlight'])) {
                        foreach ($nestedHit['highlight'] as $path => $value) {
                            foreach (['name', 'content'] as $subField) {
                                if (Str::endsWith($path, '.' . $subField)) {
                                    foreach ($value as $content) {
                                        $highlights[$subField][] = $content;
                                    }
                                }
                            }
                        }
                    }

                    $files[] = [
                        'section' => ['id' => $hit['_index']],
                        'field' => ['id' => $fieldId],
                        'material' => [
                            'id' => $hit['_source']['id'],
                            'name' => $hit['_source']['name'],
                        ],
                        'file' => $nestedHit['_source'],
                        'highlights' => $highlights
                    ];
                }
            }
        }

        return collect($files);
    }

    public function searchMaterials(
        ?string $queryString,
        array $sort,
        array $filter,
        Section\Field\Collection $fields,
        string $index,
    ): Collection
    {
        $highlightFields = ['name' => (object)[]];

        foreach ($fields as $field) {
            $highlightFields[$field->id] = (object)[];
        }

        $body = [
            'query' => ['bool' => ['must' => [['bool' => ['should' => []]]]]],
            'sort' => [[$sort['field'] => $sort['direction']]],
            'highlight' => ['fields' => $highlightFields],
            '_source' => ['id', 'name', 'created_at', ...$fields->presentInCard()->pluck('id')],
        ];

        if (!empty($queryString)) {
            $body['query']['bool']['must'][] = [['query_string' => [
                'query' => $queryString,
                'fields' => array_keys($highlightFields),
            ]]];
        }

        foreach ($filter as $fieldId => $value) {
            if ($field = $fields->where('id', $fieldId)->first()) {
                /** @var Section\Field $field */
                $body['query']['bool']['must'][0]['bool']['should'][] = $field->getFilter($value);
            }
        }

        $response = $this->client->search(compact('body', 'index'));

        $materials = [];

        foreach ($response['hits']['hits'] as $hit) {
            $materials[] = [
                'section' => ['id' => $hit['_index']],
                'material' => $hit['_source'],
                'highlight' => $hit['highlight'] ?? (object)[],
            ];
        }

        return collect($materials);
    }
}
