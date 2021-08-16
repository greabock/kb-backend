<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $url
 * @property string $realpath
 * @property string $name
 * @property bool $indexed
 * @property string|null $content
 * @property string|null $extension
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUrl($value)
 * @method static Builder|File whereContent($value)
 * @method static Builder|File whereExtension($value)
 * @method static Builder|File whereFilename($value)
 * @method static Builder|File whereIndexed($value)
 * @method static Builder|File whereRealpath($value)
 * @method static Builder|File whereName($value)
 * @mixin Eloquent
 */
class File extends Model
{
    protected $table = 'files';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'id',
        'realpath',
        'url',
        'indexed',
        'content',
        'name',
        'extension',
    ];
}
