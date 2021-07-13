<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Create;

use App\Http\Actions\Api\ApiRequest;
use App\Validation\Rules\FieldType;
use Illuminate\Http\Request as BaseRequest;

class Request extends ApiRequest
{
    public function rules(BaseRequest $request): array
    {
        $data = $request->all();

        foreach ($request->get('fields') as $index => $field) {
            $typeRules[] = FieldType::resolveRules("fields.{$index}.type", data_get($data, "fields.{$index}.type"));
        }

        $rules = array_merge([
            'id' => 'sometimes|uuid|unique:enums,id',
            'title' => 'required|string|max:255',
            'image' => 'sometimes|string|max:255',
            'is_dictionary' => 'required|boolean',
            'is_navigation' => 'required|boolean',
            'fields' => 'sometimes|array',
            'fields.*.id' => 'sometimes|uuid|unique:enum_values,id',
            'fields.*.title' => 'required|string|max:255',
            'fields.*.description' => 'sometimes|string|max:255',
            'fields.*.sort_index' => 'sometimes|integer',
            'fields.*.required' => 'required|boolean',
            'fields.*.use_in_card' => 'required|boolean',
            'fields.*.type' => 'required|array',
        ],
            ...$typeRules
        );

        return $rules;
    }

    public function struct(): array
    {
        return [
            'id',
            'title',
            'image',
            'is_dictionary',
            'is_navigation',
            'sort_index',
            'fields' => [[
                'id',
                'title',
                'description',
                'sort_index',
                'required',
                'use_in_card',
                'type',
            ]]
        ];
    }
}
