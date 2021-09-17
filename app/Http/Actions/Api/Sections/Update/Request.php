<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Update;

use App\Http\Actions\Api\ApiRequest;
use App\Models\Section;
use App\Validation\Rules\FieldType;
use Illuminate\Http\Request as BaseRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="SectionUpdateRequest",
 *     @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *     @OA\Property(property="title", type="string", example="Имя раздела", minimum="1", maximum="255"),
 *     @OA\Property(property="is_dictionary", type="boolean"),
 *     @OA\Property(property="is_navigation", type="boolean"),
 *     @OA\Property(property="image", type="string", example="Имя раздела", nullable=true, minimum="1", maximum="255"),
 *     @OA\Property(property="access", type="string", enum={"all", "only", "except"}),
 *     @OA\Property(property="config", type="object", nullable=true,
 *       @OA\Property(property="name", type="string", example="Наименование"),
 *       @OA\Property(property="description", type="string", example="Что-то тоам про поле 'name'"),
 *     ),
 *     @OA\Property(property="users", type="array",
 *       @OA\Items(type="object", required={"id"},
 *          @OA\Property(property="id", type="integer", example="123"),
 *       )
 *     ),
 *     @OA\Property(property="groups", type="array",
 *       @OA\Items(type="object", required={"id"},
 *          @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *       )
 *     ),
 *     @OA\Property(property="fields", type="array",
 *       @OA\Items(type="object",
 *           @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *           @OA\Property(property="title", type="string"),
 *           @OA\Property(property="description", type="string"),
 *           @OA\Property(property="sort_index", type="integer"),
 *           @OA\Property(property="required", type="boolean"),
 *           @OA\Property(property="is_present_in_card", type="boolean"),
 *           @OA\Property(property="filter_sort_index", type="integer"),
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
 *                 @OA\Schema(ref="#components/schemas/TypeSelect"),
 *                 @OA\Schema(ref="#components/schemas/TypeDate"),
 *          }),
 *       )
 *    )
 * )
 * @property Section $section
 */
class Request extends ApiRequest
{
    public function authorize()
    {
        return $this->section->hasAccess($this->user());
    }

    public function rules(BaseRequest $request): array
    {
        $data = $request->all();

        $typeRules = [];

        foreach ($request->get('fields', []) as $index => $field) {
            if ($type = data_get($data, "fields.{$index}.type")) {
                $typeRules[] = FieldType::resolveRules("fields.{$index}.type", $type);
            }
        }

        return array_merge(
            [
                'title' => 'sometimes|string|max:255',
                'image' => 'nullable|string|max:255',
                'is_dictionary' => 'sometimes|boolean',
                'is_navigation' => 'sometimes|boolean',
                'access' => 'sometimes|nullable|in:all,only,except',
                'config' => 'nullable',
                'users' => 'sometimes|array|index_array',
                'users.*.id' => 'sometimes|integer|distinct',
                'groups' => 'sometimes|array|index_array',
                'groups.*.id' => 'sometimes|uuid|distinct',
                'fields' => 'sometimes|array|index_array',
                'fields.*.id' => 'sometimes|uuid|distinct',
                'fields.*.title' => 'sometimes|string|max:255',
                'fields.*.description' => 'nullable|string|max:255',
                'fields.*.sort_index' => 'sometimes|integer',
                'fields.*.required' => 'sometimes|boolean',
                'fields.*.is_present_in_card' => 'sometimes|boolean',
                'fields.*.filter_sort_index' => 'sometimes|nullable|integer',
                'fields.*.type' => 'sometimes|array',
            ],
            ...$typeRules
        );
    }

    public function struct(): array
    {
        return [
            'title',
            'image',
            'is_dictionary',
            'is_navigation',
            'sort_index',
            'access',
            'config' => ['name', 'description'],
            'users' => [['id']],
            'groups' => [['id']],
            'fields' => [[
                'id',
                'title',
                'description',
                'sort_index',
                'required',
                'is_present_in_card',
                'filter_sort_index',
                'type',
            ]]
        ];
    }
}
