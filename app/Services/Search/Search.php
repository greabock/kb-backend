<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Section;
use App\Validation\Rules\FieldType;
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
        if ($fields->fileFields()->isEmpty()) {
            return collect();
        }

        $body = [
            'query' => ['bool' => ['should' => []]],
            '_source' => ['includes' => ['id', 'name', 'size', 'url']],
        ];

        foreach ($fields->fileFields() as $field) {

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
                'ignore_unmapped' => true,
                'path' => $field->id,
                'query' => ['bool' => ['must' => $must]],
                'inner_hits' => [
                    '_source' => [$field->id . '.id', $field->id . '.name', $field->id . '.extension', $field->id . '.created_at'],
                    'highlight' => ['fields' => [$field->id . '.name' => (object)[], $field->id . '.content' => (object)[]]],
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

        return collect($files)->sortBy(function ($result) use ($sort) {
            return $result['file'][$sort['field']];
        }, SORT_NATURAL, $sort['direction'] === 'desc')->forPage(1, 5)->values();
    }

    public function searchMaterials(
        ?string $queryString,
        ?array $sort,
        array $filter,
        Section\Field\Collection $fields,
        string $index,
    ): Collection
    {
        if ($sort['field'] === 'name') {
            $sort['field'] = 'name.keyword';
        }

        $highlightFields = ['name' => (object)[]];

        foreach ($fields->searchableFields() as $field) {
            $highlightFields[$field->id] = (object)[];
        }

        $body = [
            'size' => 5,
            'query' => ['bool' => ['must' => [], 'should' => []]],
            'sort' => [
                [
                    $sort['field'] => [
                        'order' => $sort['direction'],
                        'unmapped_type' => 'keyword',
                    ]
                ]
            ],
            'highlight' => ['fields' => $highlightFields],
            '_source' => [
                'includes' => ['id', 'name', 'created_at', ...$fields->presentInCard()->pluck('id')],
            ],
        ];
//
        foreach ($fields->fileFields() as $fileField) {
            $body['_source']['includes'][] = $fileField->id . '.id';
            $body['_source']['includes'][] = $fileField->id . '.extension';
            $body['_source']['includes'][] = $fileField->id . '.name';
            $body['_source']['includes'][] = $fileField->id . '.created_at';
        }

        if (!empty($queryString)) {
            $body['query']['bool']['must'][] = [['query_string' => [
                'query' => $queryString,
                'fields' => array_keys($highlightFields),
            ]]];
        }

        foreach ($filter as $fieldId => $value) {

            if (!empty($value)) {
                $body['query']['bool']['must'][] = ['exists' => ['field' => $fieldId]];
            }

            $should = [];

            if ($field = $fields->where('id', $fieldId)->first()) {
                /** @var Section\Field $field */

                $should = [...$should, ... $field->getFilter($value)];

            }

            $body['query']['bool']['must'][] = [
                'bool' => [
                    'should' => $should
                ]
            ];
        }

        $response = $this->client->search(compact('body', 'index'));

        $materials = [];

        foreach ($response['hits']['hits'] as $i => $hit) {
            $materials[$i] = [
                'section' => ['id' => $hit['_index']],
                'material' => $hit['_source'],
                'highlight' => $hit['highlight'] ?? (object)[],
            ];

            $materials[$i]['material']['files_count'] = $this->countDocs($fields->fileFields(), $hit['_source']);
        }

        return collect($materials);
    }

    private function countDocs(Section\Field\Collection $fileFields, mixed $_source): int
    {
        $count = 0;

        foreach ($fileFields as $fileField) {
            if (isset($_source[$fileField->id])) {
                if ($fileField->type['name'] === FieldType::T_LIST) {
                    $count += count($_source[$fileField->id]);
                    continue;
                }

                $count++;
            }

        }


        return $count;
    }

    public function searchMaterialsByName(string $index, ?string $name)
    {
        $body = [
            'size' => 15,
            'query' => [],
            '_source' => ['includes' => ['id']],
        ];

        if ($name) {
            $body['query']['wildcard'] = [
                'name' => [
                    'value' => "*$name*",
                    'boost' => 1.0,
                ]
            ];
        }

        $result = $this->client->search([
            'index' => $index,
            'body' => $body
        ]);

        return \Arr::pluck($result['hits']['hits'], '_source.id');
    }
}
