<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Create;

use App\Http\Actions\Api\ApiRequest;
use App\Validation\Rules\FieldType;
use Illuminate\Http\Request as BaseRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="SectionCreateRequest", required={"title", "is_dictionary", "is_navigation"},
 *     @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *     @OA\Property(property="title", type="string", example="Имя раздела", minimum="1", maximum="255"),
 *     @OA\Property(property="is_dictionary", type="boolean"),
 *     @OA\Property(property="is_navigation", type="boolean"),
 *     @OA\Property(property="image", type="string", example="Имя раздела", nullable=true, minimum="1", maximum="255"),
 *     @OA\Property(property="fields", type="array",
 *       @OA\Items(type="object", required={"title", "type", "required", "is_present_in_card", "is_filterable"},
 *           @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *           @OA\Property(property="title", type="string"),
 *           @OA\Property(property="description", type="string"),
 *           @OA\Property(property="sort_index", type="integer"),
 *           @OA\Property(property="required", type="boolean"),
 *           @OA\Property(property="is_present_in_card", type="boolean"),
 *           @OA\Property(property="is_filterable", type="boolean"),
 *           @OA\Property(property="type", oneOf={
 *                 @OA\Schema(ref="#components/schemas/TypeString"),
 *                 @OA\Schema(ref="#components/schemas/TypeInteger"),
 *                 @OA\Schema(ref="#components/schemas/TypeText"),
 *                 @OA\Schema(ref="#components/schemas/TypeWiki"),
 *                 @OA\Schema(ref="#components/schemas/TypeFloat"),
 *                 @OA\Schema(ref="#components/schemas/TypeBoolean"),
 *                 @OA\Schema(ref="#components/schemas/TypeEnum"),
 *                 @OA\Schema(ref="#components/schemas/TypeFile"),
 *                 @OA\Schema(ref="#components/schemas/TypeList"),
 *          }),
 *       )
 *    )
 * )
 */
class Request extends ApiRequest
{
    public function rules(BaseRequest $request): array
    {
        $data = $request->all();

        $typeRules = [];

        foreach ($request->get('fields', []) as $index => $field) {
            $typeRules[] = FieldType::resolveRules("fields.{$index}.type", data_get($data, "fields.{$index}.type"));
        }

        $rules = array_merge([
            'id' => 'sometimes|uuid|unique:enums,id',
            'title' => 'required|string|max:255',
            'image' => 'sometimes|string|max:255',
            'is_dictionary' => 'required|boolean',
            'is_navigation' => 'required|boolean',
            'fields' => 'sometimes|array',
            'fields.*.id' => 'sometimes|uuid|distinct|unique:enum_values,id',
            'fields.*.title' => 'required|string|max:255',
            'fields.*.description' => 'sometimes|string|max:255',
            'fields.*.sort_index' => 'sometimes|integer',
            'fields.*.required' => 'required|boolean',
            'fields.*.is_present_in_card' => 'required|boolean',
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
                'is_present_in_card',
                'is_filterable',
                'type',
            ]]
        ];
    }
}
