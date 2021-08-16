<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search;

use App\Models\Section;
use Elasticsearch\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Validation\Rules\FieldType;
use App\Http\Resources\SearchResultResource;
use Validator;

class Action
{
    public function __invoke(Request $request, Client $esClient): SearchResultResource
    {
        Validator::validate($request->all(), [
            'search' => 'sometimes|string',
            'sort' => 'sometimes|array:field,direction',
            'sort.field' => 'in:created_at,name',
            'sort.direction' => 'in:asc,desc',
            'extensions' => 'sometimes|array',
            'extensions.*' => 'string',
        ]);

        $sections = Section::with('fields')->get()->keyBy('id');

        /** @var Section\Field[]|Collection $fields */
        $fields = $sections->reduce(
            fn(Collection $carry, Section $section) => $carry->push(...$section->fields),
            collect()
        );

        $index = $sections->map(fn(Section $section) => $section->id . '_write')->join(',');


        $materials = $request->has('extensions') ? collect() : $this->searchMaterials(
            $request->get('search') ?? '',
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            $fields->filter(fn(Section\Field $field) => $field->base_type['name'] !== FieldType::T_FILE),
            $index,
            $esClient,
        );

        $files = $this->searchFiles(
            $request->get('search') ?? '',
            $request->get('extensions', []),
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            $fields->filter(fn(Section\Field $field) => $field->base_type['name'] === FieldType::T_FILE),
            $index,
            $esClient,
        );

        return new SearchResultResource(compact('materials', 'files'));
    }

    private function searchMaterials(
        string $queryString,
        array $sort,
        Collection $fields, string $index, Client $esClient): Collection
    {
        $highlightFields = ['name' => (object)[]];

        foreach ($fields as $field) {
            $highlightFields[$field->id] = (object)[];
        }

        $body = [
            'query' => [
                'bool' => [
                    'must' => [
                    ]
                ]
            ],
            'sort' => [
                [$sort['field'] => $sort['direction']]
            ],
            'highlight' => ['fields' => $highlightFields],
            '_source' => ['id', 'name'],
        ];

        if (!empty($queryString)) {
            $body['query']['bool']['must'][] = [
                [
                    'query_string' => [
                        'query' => $queryString,
                        'fields' => array_keys($highlightFields),
                    ]
                ],
            ];
        }

        $response = $esClient->search(compact('body', 'index'));

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

    private function searchFiles(
        string $queryString,
        array $extensions,
        array $sort,
        Collection $fileFields,
        string $index,
        Client $esClient
    ): Collection
    {
        if ($fileFields->isEmpty()) {
            return collect();
        }

        $body = [
            'query' => ['bool' => ['should' => []]],
            '_source' => ['includes' => ['id', 'name']],
        ];


        foreach ($fileFields as $fileField) {
            $extensionMatches = [];
            foreach ($extensions as $extension) {
                $extensionMatches[] = [
                    'term' => [
                        $fileField->id . '.extension' => $extension,
                    ]
                ];
            }

            if (!empty($queryString)) {
                $must[] = [
                    'query_string' => [
                        'query' => $queryString,
                        'fields' => [
                            $fileField->id . '.name',
                            $fileField->id . '.content',
                        ]
                    ],
                ];
            }

            $body['query']['bool']['should'][] = [
                'nested' => [
                    'path' => $fileField->id,
                    'inner_hits' => [
                        '_source' => [
                            $fileField->id . '.id',
                            $fileField->id . '.name',
                            $fileField->id . '.extension',
                            $fileField->id . '.created_at',
                        ],
                        'highlight' => [
                            'fields' => [
                                $fileField->id . '.name' => (object)[],
                                $fileField->id . '.content' => (object)[],
                            ],
                        ],
                        'sort' => [
                            [$fileField->id . '.' . $sort['field'] => $sort['direction']],
                        ],
                    ],
                    'query' => [
                        'bool' => [
                            'must' => $must,
                        ]
                    ],
                ]
            ];

        }

        $response = $esClient->search(compact('body', 'index'));

        $files = [];

        foreach ($response['hits']['hits'] as $hit) {
            foreach ($hit['inner_hits'] as $fieldId => ['hits' => ['hits' => $nestedHits]]) {
                foreach ($nestedHits as $nestedHit) {
                    $highlights = [
                        'content' => [],
                        'name' => [],
                    ];

                    foreach ($nestedHit['highlight'] as $path => $value) {
                        if (\Str::endsWith($path, '.content')) {
                            foreach ($value as $content) {
                                $highlights['content'][] = $content;
                            }
                        }

                        if (\Str::endsWith($path, '.name')) {
                            foreach ($value as $name) {
                                $highlights['name'][] = $name;
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
}
