<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Section\Field;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Section
 *
 * @property int $id
 * @property string $title
 * @property string|null $image
 * @property bool $is_dictionary
 * @property bool $is_navigation
 * @property int $sort_index
 * @property string $tableName
 * @property string $class_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereIsDictionary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereIsNavigation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSortIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section\Field[] $fields
 * @property-read int|null $fields_count
 */
class Section extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'title',
        'image',
        'is_dictionary',
        'is_navigation',
        'sort_index',
    ];

    public function setIdAttribute($value)
    {
        $this->setAttribute('class_name', 'Section' . \Str::replace('-', '', $value));
        $this->attributes['id'] = $value;
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Section\Field::class, 'section_id');
    }

    public function getTableNameAttribute()
    {
        return 'sections.' . $this->id;
    }

    public function rules(?bool $required = null): array
    {
        return $this->fields->map(fn(Section\Field $field) => $field->rules($field->type, $field->id, $required))
            ->reduce(fn(array $carry, array $rules) => array_merge($carry, $rules), []);
    }

    public function struct(): array
    {
        return $this->fields->map(fn(Section\Field $field) => $field->struct())
            ->reduce(fn(array $carry, array $rules) => array_merge($carry, $rules), ['name']);
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

}
