<?php

declare(strict_types=1);

namespace App\Models\Section;

use App\Models\Enum;
use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use App\Validation\Rules\FieldType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $foreign_key
 * @property-read string $local_pivot_key
 * @property-read string $pivot_name
 * @property-read string $related_class
 * @method static \Database\Factories\Section\FieldFactory factory(...$parameters)
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
        'is_filterable',
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
            FieldType::T_ENUM, self::TYPE_FILE, self::TYPE_DICTIONARY => $type['of'] . '_id',
            default => throw new \Exception(sprintf('unknown type %s', $type['name'])),
        };
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

    public function getCast(): string
    {

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
}
