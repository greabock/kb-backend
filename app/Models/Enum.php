<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Enum
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Enum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enum query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Enum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enum whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enum whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enum\Value[] $values
 * @property-read int|null $values_count
 */
class Enum extends Model
{
    use HasFactory;

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
