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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery\Exception;
use ScoutElastic\Builders\SearchBuilder;

/**
 * App\Models\SectionField
 *
 * @method static Builder|Field newModelQuery()
 * @method static Builder|Field newQuery()
 * @method static Builder|Field query()
 * @mixin Eloquent
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $baseType
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
 * @property int $filter_sort_index
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
 * @property Carbon|null $deleted_at
 * @property-read string $foreign_key
 * @property-read string $local_pivot_key
 * @property-read string $pivot_name
 * @property-read string $related_class
 * @method static FieldFactory factory(...$parameters)
 * @method static \Illuminate\Database\Query\Builder|Field onlyTrashed()
 * @method static Builder|Field whereDeletedAt($value)
 * @method static Builder|Field whereIsFilterable($value)
 * @method static Builder|Field whereIsPresentInCard($value)
 * @method static \Illuminate\Database\Query\Builder|Field withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Field withoutTrashed()
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
        return $this->section->id . '_id';
    }

    public function getPivotNameAttribute(): string
    {
        return 'pivots.' . Str::snake($this->id);
    }

    public function getForeignKeyAttribute(array $type = null): string
    {
        $type = $type ?? $this->type;

        return match ($type['name']) {
            FieldType::T_LIST => $this->getForeignKeyAttribute($type['of']),
            FieldType::T_ENUM, self::TYPE_DICTIONARY => $type['of'] . '_id',
            self::TYPE_FILE => $this->id . '_file_id',
            default => throw new \Exception(sprintf('unknown type %s', $type['name'])),
        };
    }

    public function isRelationField(): bool
    {
        return in_array(
            $this->type['name'],
            [FieldType::T_ENUM, FieldType::T_DICTIONARY, FieldType::T_FILE, FieldType::T_LIST],
            true
        );
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
        return $this->type['name'] === FieldType::T_LIST;
    }

    public function getRelatedClassAttribute($type = null): string
    {
        $type = $type ?? $this->type;

        return match ($type['name']) {
            FieldType::T_LIST => $this->getRelatedClassAttribute($type['of']),
            FieldType::T_ENUM => Enum\Value::class,
            FieldType::T_FILE => File::class,
            FieldType::T_DICTIONARY => Section::findOrFail($type['of'])->class_name,
            default => throw new \Exception("Type [{$type['name']}] is not relation type.")
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

    public function usingInCard(): bool
    {
        return $this->getAttribute('is_present_in_card');
    }

    public function toIndex($value): mixed
    {
        return FieldType::toIndex($this->type, $value);
    }

    public function getBaseTypeAttribute()
    {
        return $this->type['name'] === FieldType::T_LIST
            ? $this->type['of']['name']
            : $this->type['name'];
    }

    public function applyFilter(SearchBuilder $builder, array $params): SearchBuilder
    {
        return match ($this->baseType) {
            FieldType::T_DATE,
            FieldType::T_FLOAT,
            FieldType::T_INTEGER => $builder->whereBetween($this->id, $params[$this->id]),
            FieldType::T_STRING,
            FieldType::T_WIKI,
            FieldType::T_TEXT,
            FieldType::T_DICTIONARY,
            FieldType::T_ENUM,
            FieldType::T_FILE,
            FieldType::T_BOOLEAN => $builder->whereMatch($this->id, $params[$this->id]),
            default => throw new \Exception("Unknown base type [$this->baseType]")
        };
    }
}
