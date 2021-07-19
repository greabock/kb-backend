<?php

declare(strict_types=1);

namespace App\Models\Enum;

use Eloquent;
use App\Models\Enum;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Enum\Value
 *
 * @method static Builder|Value newModelQuery()
 * @method static Builder|Value newQuery()
 * @method static Builder|Value query()
 * @mixin Eloquent
 * @property int $id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Value whereCreatedAt($value)
 * @method static Builder|Value whereId($value)
 * @method static Builder|Value whereTitle($value)
 * @method static Builder|Value whereUpdatedAt($value)
 * @property string $enum_id
 * @property-read Enum $of
 * @method static Builder|Value whereEnumId($value)
 */
class Value extends Model
{
    use HasFactory;

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
