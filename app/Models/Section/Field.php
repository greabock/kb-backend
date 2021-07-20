<?php

declare(strict_types=1);

namespace App\Models\Section;

use App\Models\Enum;
use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Mockery\Exception;

/**
 * App\Models\SectionField
 *
 * @method static Builder|Field newModelQuery()
 * @method static Builder|Field newQuery()
 * @method static Builder|Field query()
 * @mixin \Eloquent
 * @property string $id
 * @property string $title
 * @property string $description
 * @property int $sort_index
 * @property mixed $type
 * @property mixed $required
 * @property bool $is_present_in_card
 * @property string $section_id
 * @property string $columnName
 * @property string $pivotName
 * @property string $foreignKey
 * @property string $localPivotKey
 * @property string $relatedClass
 * @property string $is_filterable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Field whereCreatedAt($value)
 * @method static Builder|Field whereDescription($value)
 * @method static Builder|Field whereId($value)
 * @method static Builder|Field whereRequired($value)
 * @method static Builder|Field whereSectionId($value)
 * @method static Builder|Field whereSortIndex($value)
 * @method static Builder|Field whereTitle($value)
 * @method static Builder|Field whereType($value)
 * @method static Builder|Field whereUpdatedAt($value)
 * @method static Builder|Field whereUseInCard($value)
 * @property-read Section $section
 */
class Field extends Model
{
    use HasFactory;

    protected $touches = ['section'];

    private const SCALAR_TYPES = [
        'String',
        'Integer',
        'Float',
        'Boolean',
        'Text',
        'Wiki',
    ];

    protected $table = 'section_fields';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'sort_index',
        'type',
        'required',
        'is_present_in_card',
        'is_filterable',
    ];

    protected $casts = [
        'type' => 'array'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function getPivotNameAttribute(): string
    {
        return 'pivots.' . Str::snake($this->id);
    }

    public function usingInCard(): bool
    {
        return $this->getAttribute('is_present_in_card');
    }

    public function getForeignKeyAttribute(array $type = null): string
    {
        $type = $type ?? $this->type;

        return match ($type['name']) {
            'List' => $this->getForeignKeyAttribute($type['of']),
            'Enum', 'File', 'Dictionary' => $type['of'] . '_id',
            default => throw new \Exception(sprintf('unknown type %s', $type['name'])),
        };
    }

    public function getLocalPivotKeyAttribute(): string
    {
        return $this->section->id . '_id';
    }

    public function rules(array $type = null, string $field = null, ?bool $required = null): array
    {
        $type = $type ?? $this->type;
        $field = $field ?? $this->id;
        $required = $required ?? $this->required;

        return match ($type['name']) {
            'String' => [$field => [
                $required ? 'required' : 'sometimes',
                'string',
                'min:' . ($type['min'] ?? 0),
                'max:' . ($type['max'] ?? 255),
            ]],

            'Integer' => [$field => [
                $required ? 'required' : 'sometimes',
                'integer',
                'min:' . ($type['min'] ?? -2147483647),
                'max:' . ($type['max'] ?? 2147483647),
            ]],
            'Float' => [$field => [
                $required ? 'required' : 'sometimes',
                'number',
                'min:' . ($type['min'] ?? -2147483647),
                'max:' . ($type['max'] ?? 2147483647),
            ]],
            'Boolean' => [$field => [
                $required ? 'required' : 'sometimes',
                'boolean',
            ]],

            'Text', 'Wiki' => [$field => [
                $required ? 'required' : 'sometimes',
                'string',
                'min:' . ($type['min'] ?? 0),
                'max:' . ($type['max'] ?? 21845),
            ]],
            'Enum' => [
                $field => [$required ? 'required' : 'sometimes', 'array:id'],
                $field . '.id' => [
                    $required ? 'required' : 'sometimes',
                    'uuid',
                    'exists:enum_values,id',
                ]
            ],
            'File' => [
                $field => [$required ? 'required' : 'sometimes', 'array:id'],
                $field . '.id' => [
                    $required ? 'required' : 'sometimes',
                    'uuid',
                    'exists:files,id',
                ]
            ],
            'Dictionary' => [
                $field => [$required ? 'required' : 'sometimes', 'array:id'],
                $field . '.id' => [
                    $required ? 'required' : 'sometimes',
                    'uuid',
                    "exists:pgsql.sections.{$type['of']},id",
                ]
            ],
            'List' => $this->buildSubRules($type, $field, $required),
            default => throw new \Exception('Unknown type ' . $this->type['name'])
        };
    }

    public function struct(): array
    {
        return match ($this->type['name']) {
            'String', 'Integer', 'Float', 'Boolean', 'Text', 'Wiki' => [$this->id],
            'Enum', 'File', 'Dictionary' => [$this->id => ['id']],
            'List' => [$this->id => [['id']]],
            default => throw new \Exception('Unknown type ' . $this->type['name']),
        };
    }

    private function buildSubRules(array $type, string $field, bool $required): array
    {
        $subRules = $this->rules($type['of'], '*', $required);
        $rules = [$field => [$required ? 'required' : 'sometimes']];

        foreach ($subRules as $key => $rule) {
            $rules[$field . '.' . $key] = $rule;
        }

        return $rules;
    }

    public function isRelationField(): bool
    {
        return in_array($this->type['name'], ['Enum', 'Dictionary', 'File', 'List']);
    }

    public function isBelongsTo(): bool
    {
        return in_array($this->type['name'], ['Enum', 'Dictionary', 'File']);
    }

    public function isBelongsToMany(): bool
    {
        return $this->type['name'] === 'List';
    }

    public function getRelatedClassAttribute($type = null): string
    {
        $type = $type ?? $this->type;

        return match ($type['name']) {
            'List' => $this->getRelatedClassAttribute($type['of']),
            'Enum' => Enum\Value::class,
            'File' => File::class,
            'Dictionary' => Section::findOrFail($type['of'])->class_name,
        };
    }

    public function getRelationLoader(): callable
    {
        return function (Material $that): BelongsTo|BelongsToMany {
            $relatedModel = (new ($this->relatedClass));
            return match (true) {
                $this->isBelongsTo() => $that->belongsTo(
                    $this->relatedClass,
                    $this->foreignKey,
                    $relatedModel->getKeyName(),
                    $this->id
                ),
                $this->isBelongsToMany() => $that->belongsToMany(
                    $this->relatedClass,
                    $this->pivotName,
                    $this->localPivotKey,
                    $this->foreignKey,
                    $this->getKeyName(),
                    $relatedModel->getKeyName(),
                    $this->id,
                ),
                default => throw new Exception('Unknown relation type'),
            };
        };
    }
}
