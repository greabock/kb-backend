<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Models\Enum;
use App\Models\File;
use App\Models\Material;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\Pure;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="TypeString", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"String"}, example="String"),
 *     @OA\Property(property="min", type="integer", example="0", minimum="1", maximum="255"),
 *     @OA\Property(property="max", type="integer", example="255", minimum="1", maximum="255"),
 * )
 * @OA\Schema(schema="TypeInteger", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"Integer"}, example="Integer"),
 *     @OA\Property(property="min", type="integer", example="0", minimum="-2147483647", maximum="2147483647"),
 *     @OA\Property(property="max", type="integer", example="0", minimum="-2147483647", maximum="2147483647"),
 * )
 * @OA\Schema(schema="TypeText", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"Text"}, example="Text"),
 *     @OA\Property(property="min", type="integer", example="0", minimum="1", maximum="21845"),
 *     @OA\Property(property="max", type="integer", example="0", minimum="1", maximum="21845"),
 * )
 * @OA\Schema(schema="TypeWiki", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"Wiki"}, example="Wiki"),
 *     @OA\Property(property="min", type="integer", example="0", minimum="1", maximum="21845"),
 *     @OA\Property(property="max", type="integer", example="0", minimum="1", maximum="21845"),
 * )
 * @OA\Schema(schema="TypeFloat", required={"name", "step"},
 *     @OA\Property(property="name", type="string", enum={"Float"}, example="Float"),
 *     @OA\Property(property="min", type="number", example="0.1", minimum="-2147483647", maximum="2147483647"),
 *     @OA\Property(property="max", type="number", example="255.5", minimum="-2147483647", maximum="2147483647"),
 *     @OA\Property(property="step", type="number", example="0.1", minimum="-2147483647", maximum="2147483647"),
 * )
 * @OA\Schema(schema="TypeBoolean", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"Boolean"}, example="Boolean"),
 * )
 * @OA\Schema(schema="TypeDate", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"Date"}, example="Date"),
 * )
 * @OA\Schema(schema="TypeDictionary", required={"name", "of"},
 *     @OA\Property(property="name", type="string", enum={"Dictionary"}, example="Dictionary"),
 *     @OA\Property(property="of", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 * )
 * @OA\Schema(schema="TypeEnum", required={"name", "of"},
 *     @OA\Property(property="name", type="string", enum={"Enum"}, example="Enum"),
 *     @OA\Property(property="of", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 * )
 * @OA\Schema(schema="TypeFile", required={"name", "of"},
 *     @OA\Property(property="name", type="string", enum={"File"}, example="File"),
 *     @OA\Property(property="max", type="integer", example="1", minimum="1", maximum=PHP_INT_MAX),
 *     @OA\Property(property="extensions", type="array",
 *          @OA\Items(type="string")
 *     ),
 * )
 *
 * @OA\Schema(schema="TypeSelect", required={"name"},
 *     @OA\Property(property="name", type="string", enum={"String"}, example="Select"),
 *     @OA\Property(property="of", type="array",
 *      @OA\Items(type="string")
 *    ),
 * )
 *
 * @OA\Schema(schema="TypeList", required={"name", "of"},
 *     @OA\Property(property="name", type="string", enum={"List"},  example="List"),
 *     @OA\Property(property="min", type="integer", example="1", minimum="1", maximum=PHP_INT_MAX),
 *     @OA\Property(property="max", type="integer", example="1", minimum="1", maximum=PHP_INT_MAX),
 *     @OA\Property(property="of", oneOf={
 *           @OA\Schema(ref="#components/schemas/TypeEnum"),
 *           @OA\Schema(ref="#components/schemas/TypeDictionary"),
 *           @OA\Schema(ref="#components/schemas/TypeFile"),
 *           @OA\Schema(ref="#components/schemas/TypeSelect"),
 *     }),
 * )
 */
class FieldType
{
    public const T_STRING = 'String';
    public const T_INTEGER = 'Integer';
    public const T_FLOAT = 'Float';
    public const T_BOOLEAN = 'Boolean';
    public const T_LIST = 'List';
    public const T_DICTIONARY = 'Dictionary';
    public const T_ENUM = 'Enum';
    public const T_FILE = 'File';
    public const T_TEXT = 'Text';
    public const T_WIKI = 'Wiki';
    public const T_DATE = 'Date';
    public const T_SELECT = 'Select';

    private const AVAILABLE_TYPES = [
        self::T_STRING,
        self::T_INTEGER,
        self::T_FLOAT,
        self::T_BOOLEAN,
        self::T_LIST,
        self::T_DICTIONARY,
        self::T_ENUM,
        self::T_FILE,
        self::T_TEXT,
        self::T_WIKI,
        self::T_DATE,
        self::T_SELECT,
    ];

    public const LISTABLE_TYPES = [
        self::T_ENUM,
        self::T_FILE,
        self::T_DICTIONARY,
    ];

    public const LINK_TYPES = [
        self::T_ENUM,
        self::T_FILE,
        self::T_DICTIONARY,
    ];

    public const SCALAR_TYPES = [
        self::T_STRING,
        self::T_INTEGER,
        self::T_FLOAT,
        self::T_BOOLEAN,
        self::T_TEXT,
        self::T_WIKI,
        self::T_DATE,
        self::T_SELECT,
    ];

    public static function resolveRules($attribute, array $value): array
    {
        return array_merge(
            [self::prefix($attribute, 'name') => ['required', Rule::in(self::AVAILABLE_TYPES)]],
            call_user_func([self::class, 'rules' . $value['name']], $attribute, $value)
        );
    }

    // String
    public static function rulesString($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'min') => 'sometimes|integer|lte:' . self::prefix($attribute, 'max') . '|min:0|max:255',
            self::prefix($attribute, 'max') => 'sometimes|integer|gte:' . self::prefix($attribute, 'min') . '|min:0|max:255',
        ];
    }

    // Date
    public static function rulesDate($attribute, $value): array
    {
        return [];
    }


    // Text
    public static function rulesText($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'min') => 'sometimes|integer|lte:' . self::prefix($attribute, 'max') . '|min:0|max:21844',
            self::prefix($attribute, 'max') => 'sometimes|integer|gte:' . self::prefix($attribute, 'min') . '|min:0|max:21844',
        ];
    }

    // Wiki
    public function rulesWiki($attribute, $value): array
    {
        return self::rulesText($attribute, $value);
    }

    // Integer
    public static function rulesInteger($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'min') => 'sometimes|integer|lte:' . self::prefix($attribute, 'max') . '|min:' . PHP_INT_MIN . '|max:' . PHP_INT_MAX,
            self::prefix($attribute, 'max') => 'sometimes|integer|gte:' . self::prefix($attribute, 'min') . '|min:' . PHP_INT_MIN . '|max:' . PHP_INT_MAX,
        ];
    }

    // Float
    public static function rulesFloat($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'min') => 'sometimes|numeric|lte:' . self::prefix($attribute, 'max') . '|min:' . -PHP_FLOAT_MAX . '|max:' . PHP_FLOAT_MAX,
            self::prefix($attribute, 'max') => 'sometimes|numeric|gte:' . self::prefix($attribute, 'min') . '|min:' . -PHP_FLOAT_MAX . '|max:' . PHP_FLOAT_MAX,
            self::prefix($attribute, 'step') => 'required|numeric|min:' . PHP_FLOAT_MIN . '|max:' . PHP_FLOAT_MAX
        ];
    }

    // Boolean
    public static function rulesBoolean($attribute, $value): array
    {
        return [];
    }

    public static function rulesSelect($attribute, $value)
    {
        return [
            'of' => 'array|min:1',
            'of.*' => 'required|string',
        ];
    }

    // List
    public static function rulesList($attribute, $value): array
    {
        $rules = [
            self::prefix($attribute, 'min') => 'sometimes|lte:' . self::prefix($attribute, 'max') . '|min:0|max:' . PHP_INT_MAX,
            self::prefix($attribute, 'max') => 'sometimes|gte:' . self::prefix($attribute, 'min') . '|min:0|max:' . PHP_INT_MAX,
            $of = self::prefix($attribute, 'of') => 'required|array'
        ];

        return array_merge($rules, self::resolveRules($of, $value['of']));
    }

    // Dictionary
    public static function rulesDictionary($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'of') => 'required|uuid|exists:sections,id'
        ];
    }

    // Enum
    public static function rulesEnum($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'of') => 'required|uuid|exists:enums,id'
        ];
    }

    public static function rulesFile($attribute, $value): array
    {
        return [
            self::prefix($attribute, 'max') => 'sometimes|min:0|max:' . PHP_INT_MAX,
            self::prefix($attribute, 'extensions') => 'array',
            self::prefix($attribute, 'extensions.*') => 'string|distinct',
        ];
    }

    private static function prefix($attribute, $field): string
    {
        return implode('.', [$attribute, $field]);
    }


    public static function rules(array $type, string $field, ?bool $required)
    {
        return match ($type['name']) {
            self::T_STRING => [$field => [
                $required ? 'required' : 'sometimes',
                'string',
                'min:' . ($type['min'] ?? 0),
                'max:' . ($type['max'] ?? 255),
            ]],
            self::T_DATE => [$field => [
                $required ? 'required' : 'sometimes',
                'date',
            ]],
            self::T_INTEGER => [$field => [
                $required ? 'required' : 'sometimes',
                'integer',
                'min:' . ($type['min'] ?? -2147483647),
                'max:' . ($type['max'] ?? 2147483647),
            ]],
            self::T_FLOAT => [$field => [
                $required ? 'required' : 'sometimes',
                'number',
                'min:' . ($type['min'] ?? -2147483647),
                'max:' . ($type['max'] ?? 2147483647),
            ]],
            self::T_BOOLEAN => [$field => [
                $required ? 'required' : 'sometimes',
                'boolean',
            ]],
            self::T_TEXT, self::T_WIKI => [$field => [
                $required ? 'required' : 'sometimes',
                'string',
                'min:' . ($type['min'] ?? 0),
                'max:' . ($type['max'] ?? 21845),
            ]],

            self::T_ENUM => [
                $field => [$required ? 'required' : 'sometimes', 'array:id'],
                $field . '.id' => [
                    $required ? 'required' : 'sometimes',
                    'uuid',
                    'exists:enum_values,id',
                ]
            ],
            self::T_FILE => [
                $field => [$required ? 'required' : 'sometimes', 'array:id'],
                $field . '.id' => [
                    $required ? 'required' : 'sometimes',
                    'uuid',
                    'exists:files,id',
                ]
            ],
            self::T_DICTIONARY => [
                $field => [$required ? 'required' : 'sometimes', 'array:id'],
                $field . '.id' => [
                    $required ? 'required' : 'sometimes',
                    'uuid',
                    "exists:pgsql.sections.{$type['of']},id",
                ]
            ],

            self::T_SELECT => [
                $field => [
                    $required ? 'required' : 'sometimes',
                    'in:' . implode(',', $type['of']),
                ],
            ],

            self::T_LIST => self::buildSubRules($type, $field, $required),
            default => throw new Exception('Unknown type ' . $type['name'])
        };
    }

    private static function buildSubRules(array $type, string $field, bool $required): array
    {
        $rules = [$field => [$required ? 'required' : 'sometimes']];

        foreach (self::rules($type['of'], '*', $required) as $key => $rule) {
            $rules[$field . '.' . $key] = $rule;
        }

        return $rules;
    }

    public static function struct($type, $attribute)
    {
        return match (true) {
            in_array($type['name'], self::SCALAR_TYPES, true) => [$attribute],
            in_array($type['name'], self::LINK_TYPES, true) => [$attribute => ['id']],
            self::T_LIST === $type['name'] => match (true) {
                $type['of']['name'] === self::T_SELECT => [$attribute],
                default => [$attribute => [['id']]],
            },
            default => throw new Exception('Unknown type ' . $type),
        };
    }

    public static function getCast($type): string
    {
        return match ($type) {
            self::T_STRING, self::T_TEXT, self::T_WIKI, self::T_SELECT => 'string',
            self::T_DATE => 'date',
            self::T_INTEGER => 'integer',
            self::T_FLOAT => 'float',
            self::T_BOOLEAN => 'boolean',
            self::T_LIST => 'array',
            default => null,
        };
    }

    public static function getElasticConfig(string $type): array
    {
        return match ($type) {
            self::T_TEXT, self::T_WIKI, self::T_STRING, self::T_FILE => [
                'type' => 'text',
                'analyzer' => 'ru'
            ],
            self::T_DATE => [
                'type' => 'date',
            ],
            self::T_INTEGER => [
                'type' => 'integer'
            ],
            self::T_FLOAT => [
                'type' => 'float'
            ],
            self::T_BOOLEAN => [
                'type' => 'boolean'
            ],
            self::T_ENUM, self::T_DICTIONARY, self::T_SELECT => [
                'type' => 'keyword'
            ],
        };
    }

    public static function toIndex(
        array $type,
        Collection|Enum\Value|Material|File|DateTime|array|string|float|int|bool|null $value,
    ): mixed
    {
        if ($type['name'] === self::T_LIST) {

            if ($type['of']['name'] === self::T_SELECT) {
                return $value;
            }

            return $value->map(fn($el) => self::toIndex($type['of'], $el))->toArray();
        }

        return match ($type['name']) {
            self::T_ENUM, self::T_DICTIONARY => $value->id,
            self::T_FILE => $value->content,
            self::T_DATE => $value?->format(DATE_W3C),
            default => $value,
        };
    }
}
