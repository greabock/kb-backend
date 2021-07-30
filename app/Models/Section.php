<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Collection as SupportCollection;
use JetBrains\PhpStorm\ArrayShape;
use Str;
use Eloquent;
use App\Models\Section\Field;
use Illuminate\Support\Carbon;
use App\Validation\Rules\FieldType;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Section
 *
 * @property string $id
 * @property string $title
 * @property string|null $image
 * @property bool $is_dictionary
 * @property bool $is_navigation
 * @property bool $indexing
 * @property int $sort_index
 * @property string $tableName
 * @property string $class_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Section newModelQuery()
 * @method static Builder|Section newQuery()
 * @method static Builder|Section query()
 * @method static Builder|Section whereCreatedAt($value)
 * @method static Builder|Section whereId($value)
 * @method static Builder|Section whereImage($value)
 * @method static Builder|Section whereIsDictionary($value)
 * @method static Builder|Section whereIsNavigation($value)
 * @method static Builder|Section whereSortIndex($value)
 * @method static Builder|Section whereTitle($value)
 * @method static Builder|Section whereUpdatedAt($value)
 * @property-read Collection|Field[] $fields
 * @property-read int|null $fields_count
 * @property Carbon|null $deleted_at
 * @property-read mixed $table_name
 * @method static SectionFactory factory(...$parameters)
 * @method static QueryBuilder|Section onlyTrashed()
 * @method static Builder|Section whereClassName($value)
 * @method static Builder|Section whereDeletedAt($value)
 * @method static QueryBuilder|Section withTrashed()
 * @method static QueryBuilder|Section withoutTrashed()
 * @mixin Eloquent
 */
class Section extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'title',
        'image',
        'is_dictionary',
        'is_navigation',
        'sort_index',
    ];

    protected array $defaultMapping = [
        'id' => ['type' => 'keyword'],
        'name' => ['type' => 'text', 'analyzer' => 'ru'],
        'created_at' => ['type' => 'date'],
        'updated_at' => ['type' => 'date'],
    ];

    public function setIdAttribute($value)
    {
        $this->setAttribute('class_name', 'Section' . Str::replace('-', '', $value));
        $this->attributes['id'] = $value;
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Section\Field::class, 'section_id')->orderBy('sort_index');
    }

    public function getTableNameAttribute()
    {
        return 'sections.' . $this->id;
    }

    public function rules(?bool $required = null): array
    {
        return $this->fields->map(fn(Section\Field $field) => FieldType::rules(
            $field->type,
            $field->id,
            $required ?? $field->required,
        ))->reduce(fn(array $carry, array $rules) => array_merge($carry, $rules), []);
    }

    public function struct(): array
    {
        return $this->fields->map(
            fn(Section\Field $field) => FieldType::struct($field->type['name'], $field->id)
        )->reduce(fn(array $carry, array $rules) => array_merge($carry, $rules), ['name']);
    }

    public function getRelationFields(): Collection
    {
        return $this->fields->filter(fn(Field $field) => $field->isRelationField());
    }

    public function plainFieldKeys(): array
    {
        return $this->plainFields()->pluck('id')->toArray();
    }

    public function plainFields(): Collection
    {
        return $this->fields->filter(fn(Field $field) => !$field->isRelationField());
    }

    public function relationLoaders(): array
    {
        return $this->getRelationFields()
            ->keyBy('id')
            ->map(fn(Field $field) => $field->getRelationLoader())
            ->toArray();
    }

    public function cardFields(): array
    {
        return $this
            ->fields
            ->filter(fn(Section\Field $field) => $field->usingInCard())
            ->reduce(fn($carry, Field $field) => [...$carry, $field->id], ['name']);
    }

    public function getFieldCasts(): array
    {
        return $this->plainFields()->keyBy('id')
            ->map(fn(Field $field) => FieldType::getCast($field->type['name']))
            ->filter()
            ->toArray();
    }

    public function getMaterialMappings(): array
    {
        return ['properties' => array_merge(
            $this->defaultMapping,
            $this->fields->keyBy('id')
                ->map(fn(Field $field) => FieldType::getElasticConfig($field->baseType))
                ->toArray(),
        )];
    }
}
