<?php

declare(strict_types=1);

namespace App\Models\Section;

use App\Models\Enum;
use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use App\Validation\Rules\FieldType;
use Database\Factories\Section\FieldFactory;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * App\Models\SectionField
 *
 * @mixin Eloquent
 * @property string $id
 * @property string $title
 * @property string $description
 * @property array $baseType
 * @property int $sort_index
 * @property mixed $type
 * @property mixed $required
 * @property bool $is_present_in_card
 * @property string $section_id
 * @property int $filter_sort_index
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Section $section
 * @property-read string $foreign_key
 * @property-read string $local_pivot_key
 * @property-read string $pivot_name
 * @property-read string $related_class
 * @property-read array $base_type
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
 * @method static Builder|Field whereDeletedAt($value)
 * @method static Builder|Field whereIsFilterable($value)
 * @method static Builder|Field whereIsPresentInCard($value)
 * @method static Builder|Field newModelQuery()
 * @method static Builder|Field newQuery()
 * @method static Builder|Field query()
 * @method static QueryBuilder|Field onlyTrashed()
 * @method static QueryBuilder|Field withTrashed()
 * @method static QueryBuilder|Field withoutTrashed()
 * @method static Builder|Field whereFilterSortIndex($value)
 * @method static FieldFactory factory(...$parameters)
 */
class Field extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_ENUM = 'Enum';
    public const TYPE_FILE = 'File';
    public const TYPE_DICTIONARY = 'Dictionary';
    public const TYPE_LIST = 'List';
    public const TYPE_DATE = 'Date';

    protected $touches = ['section'];

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
        'filter_sort_index',
    ];

    protected $casts = [
        'type' => 'array'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function getLocalPivotKeyAttribute(): string
    {
        return $this->section_id . '_id';
    }

    public function getPivotNameAttribute(): string
    {
        return 'pivots.' . $this->id;
    }

    public function getForeignKeyAttribute(array $type = null): string
    {
        $type = $type ?? $this->type;

        return match ($type['name']) {
            FieldType::T_LIST => $this->getForeignKeyAttribute($type['of']),
            FieldType::T_ENUM,
            self::TYPE_DICTIONARY => $type['of'] . '_id',
            self::TYPE_FILE => $this->id . '_file_id',
            default => throw new Exception(sprintf('unknown type %s', $type['name'])),
        };
    }

    public function isRelationField(): bool
    {
        return $this->isBelongsTo() || $this->isBelongsToMany();
    }

    public function isBelongsTo(): bool
    {
        return in_array(
            $this->type['name'],
            [FieldType::T_ENUM, FieldType::T_DICTIONARY, FieldType::T_FILE],
            true
        );
    }

    public function isBelongsToMany(): bool
    {
        return $this->type['name'] === FieldType::T_LIST
            && $this->type['of']['name'] !== FieldType::T_SELECT;
    }

    public function getRelatedClassAttribute($type = null): string
    {
        $type = $type ?? $this->type;

        return match ($type['name']) {
            FieldType::T_LIST => $this->getRelatedClassAttribute($type['of']),
            FieldType::T_ENUM => Enum\Value::class,
            FieldType::T_FILE => File::class,
            FieldType::T_DICTIONARY => Section::findOrFail($type['of'])->class_name,
            default => throw new Exception("Type [{$type['name']}] is not relation type.")
        };
    }

    public function isPlainField(): bool
    {
        return !$this->isRelationField();
    }

    public function getRelationLoader(): callable
    {
        return function (Material $that): BelongsTo|BelongsToMany {
            $relatedModel = (new ($this->related_class));
            return match (true) {

                $this->isBelongsTo() => $that->belongsTo(
                    $this->related_class,
                    $this->foreign_key,
                    $relatedModel->getKeyName(),
                    $this->id
                ),

                $this->isBelongsToMany() => $that->belongsToMany(
                    $this->related_class,
                    $this->pivot_name,
                    $this->local_pivot_key,
                    $this->foreign_key,
                    $this->getKeyName(),
                    $relatedModel->getKeyName(),
                    $this->id,
                ),
                default => throw new Exception('Unknown relation type'),
            };
        };
    }

    public function usingInCard(): bool
    {
        return $this->getAttribute('is_present_in_card');
    }

    public function toIndex($value): mixed
    {
        return FieldType::toIndex($this->type, $value);
    }

    public function getBaseTypeAttribute(): array
    {
        return $this->type['name'] === FieldType::T_LIST
            ? $this->type['of']
            : $this->type;
    }

    public function getFilter($values): array
    {
        if (is_array($values)) {
            return array_map(function ($value) {
                return match ($this->base_type['name']) {
                    FieldType::T_DATE,
                    FieldType::T_FLOAT,
                    FieldType::T_INTEGER => ['range' => [$this->id => ['gte' => $value[0], 'lte' => $value[1]]]],
                    FieldType::T_STRING,
                    FieldType::T_WIKI,
                    FieldType::T_TEXT,
                    FieldType::T_DICTIONARY,
                    FieldType::T_ENUM,
                    FieldType::T_FILE,
                    FieldType::T_SELECT,
                    FieldType::T_BOOLEAN => ['term' => [$this->id => $value]],
                    default => throw new Exception("Unknown base type [{$this->base_type['name']}]")
                };
            }, $values);
        }
        // TODO выпилить
        return [['term' => [$this->id => (bool)$values]]];
    }

    public function newCollection(array $models = []): Field\Collection
    {
        return new Field\Collection($models);
    }

    public function isSameType(Field $field): bool
    {
        return $this->sameTypeArray($this->type, $field->type);
    }

    private function sameTypeArray(array $a, array $b): bool
    {
        if ($a['name'] !== $b['name']) {
            return false;
        }

        if (!empty($a['of']) && !empty($b['of'])) {
            if (gettype($a['of']) !== gettype($b['of'])) {
                return false;
            }

            if (is_array($a['of'])) {

                if ($a['name'] === FieldType::T_SELECT) {
                    return empty(array_merge(
                        array_diff($a['of'], $b['of']),
                        array_diff($b['of'], $a['of'])
                    ));
                }

                return $this->sameTypeArray($a['of'], $b['of']);
            }

            return $a['of'] === $b['of'];
        }

        if (isset($a['of']) xor isset($b['of'])) {
            return false;
        }

        return true;
    }
}
