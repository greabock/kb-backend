<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use App\Models\Enum\Value;
use Database\Factories\EnumFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * App\Models\Enum
 *
 * @method static Builder|Enum newModelQuery()
 * @method static Builder|Enum newQuery()
 * @method static Builder|Enum query()
 * @mixin Eloquent
 * @property int $id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Enum whereCreatedAt($value)
 * @method static Builder|Enum whereId($value)
 * @method static Builder|Enum whereTitle($value)
 * @method static Builder|Enum whereUpdatedAt($value)
 * @property-read Collection|Value[] $values
 * @property-read int|null $values_count
 * @property Carbon|null $deleted_at
 * @method static EnumFactory factory(...$parameters)
 * @method static QueryBuilder|Enum onlyTrashed()
 * @method static Builder|Enum whereDeletedAt($value)
 * @method static QueryBuilder|Enum withTrashed()
 * @method static QueryBuilder|Enum withoutTrashed()
 */
class Enum extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'enums';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'title'
    ];

    public function values(): HasMany
    {
        return $this->hasMany(Enum\Value::class, 'enum_id');
    }
}
