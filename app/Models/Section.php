<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Actions\Auth\Me\Action;
use App\Models\Section\Field;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Ramsey\Collection\Collection;
use Schema;
use Str;

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

    public function fields(): HasMany
    {
        return $this->hasMany(Section\Field::class, 'section_id');
    }

    public function getTableNameAttribute()
    {
        return 'sections.' . Str::snake($this->id);
    }

    public function build(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->uuid('id')->primary();
        });
    }

    public function drop(): void
    {
        Schema::dropIfExists($this->tableName);
    }
}
