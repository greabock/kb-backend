<?php

declare(strict_types=1);

namespace App\Models\Enum;

use Database\Factories\Enum\ValueFactory;
use Eloquent;
use App\Models\Enum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as BuilderAlias;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Enum\Value
 * @property int $id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $enum_id
 * @property Carbon|null $deleted_at
 * @property-read Enum $of
 * @method static Builder|Value newModelQuery()
 * @method static Builder|Value newQuery()
 * @method static Builder|Value query()
 * @method static Builder|Value whereCreatedAt($value)
 * @method static Builder|Value whereId($value)
 * @method static Builder|Value whereTitle($value)
 * @method static Builder|Value whereUpdatedAt($value)
 * @method static Builder|Value whereEnumId($value)
 * @method static ValueFactory factory(...$parameters)
 * @method static BuilderAlias|Value onlyTrashed()
 * @method static Builder|Value whereDeletedAt($value)
 * @method static BuilderAlias|Value withTrashed()
 * @method static BuilderAlias|Value withoutTrashed()
 * @mixin Eloquent
 */
class Value extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'enum_values';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'title',
    ];

    public function of(): BelongsTo
    {
        return $this->belongsTo(Enum::class, 'enum_id');
    }
}
