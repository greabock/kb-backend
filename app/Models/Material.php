<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use ScoutElastic\Builders\FilterBuilder;
use ScoutElastic\Builders\SearchBuilder;

/**
 * @property string $id
 * @property string $name
 * @property string $sectionId
 * @property Section $section
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class Material extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';

    public static $sectionId = null;

    protected $keyType = 'string';

    public $incrementing = false;

    public static function configure(Section $section): void
    {
        foreach ($section->relationLoaders() as $key => $loader) {
            static::resolveRelationUsing($key, $loader);
        }
    }

    protected static function booted()
    {
        static::configure(Section::findOrFail(static::$sectionId));
        parent::booted();
    }

    public function toIndex(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at->format(DATE_W3C),
            'updated_at' => $this->updated_at->format(DATE_W3C),
        ];

        foreach ($this->section->fields as $field) {
            $data[$field->id] = $field->toIndex($this->getAttribute($field->id));
        }

        return $data;
    }

    public function getSectionIdAttribute()
    {
        return static::$sectionId;
    }

    public function getSectionAttribute(): Section
    {
        return Section::find(static::$sectionId);
    }

    /**
     * Execute the search.
     *
     * @param string $query
     * @param callable|null $callback
     * @return \ScoutElastic\Builders\FilterBuilder|\ScoutElastic\Builders\SearchBuilder
     */
    public static function search($query, $callback = null)
    {
        $softDelete = static::usesSoftDelete() && config('scout.soft_delete', false);

        if ($query === '*') {
            return new FilterBuilder(new static, $callback, $softDelete);
        } else {
            return new SearchBuilder(new static, $query, $callback, $softDelete);
        }
    }

    public function getMapping(): array
    {
        return $this->section->getMaterialMappings();
    }
}
