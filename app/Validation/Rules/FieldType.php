<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

class FieldType
{
    private const T_STRING = 'String';
    private const T_INTEGER = 'Integer';
    private const T_FLOAT = 'Float';
    private const T_BOOLEAN = 'Boolean';
    private const T_LIST = 'List';
    private const T_DICTIONARY = 'Dictionary';
    private const T_ENUM = 'Enum';
    private const T_FILE = 'File';
    private const T_TEXT = 'Text';
    private const T_WIKI = 'Wiki';

    private const AVAILABLE_TYPES = [
        self::T_STRING,
        self::T_INTEGER,
        self::T_FLOAT,
        self::T_BOOLEAN,
        self::T_LIST,
        self::T_DICTIONARY,
        self::T_ENUM,
        self::T_FILE,
    ];

    private const LISTED_TYPES = [
        self::T_DICTIONARY,
        self::T_ENUM,
        self::T_FILE,
    ];

    public static function resolveRules($attribute, array $value): array
    {
        return array_merge(
            [self::prefix($attribute, 'name') => ['required', \Illuminate\Validation\Rule::in(self::AVAILABLE_TYPES)]],
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
}
