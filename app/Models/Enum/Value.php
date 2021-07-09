<?php

declare(strict_types=1);

namespace App\Models\Enum;

use App\Models\Enum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Enum\Value
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Value newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Value newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Value query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Value whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Value whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Value whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Value whereUpdatedAt($value)
 * @property string $enum_id
 * @property-read Enum $of
 * @method static \Illuminate\Database\Eloquent\Builder|Value whereEnumId($value)
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
