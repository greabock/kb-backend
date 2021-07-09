<?php

declare(strict_types=1);

namespace App\Models\Section;

use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SectionField
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $sort_index
 * @property mixed $type
 * @property mixed $required
 * @property bool $use_in_card
 * @property string $section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereSortIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereUseInCard($value)
 * @property-read Section $section
 */
class Field extends Model
{
    protected $table = 'section_fields';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'sort_index',
        'type',
        'required',
        'use_in_card',
    ];

    protected $casts = [
        'type' => 'array'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
