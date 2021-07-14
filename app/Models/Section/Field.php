<?php

declare(strict_types=1);

namespace App\Models\Section;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Schema;

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
 * @property string $columnName
 * @property string $tableName
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
    use HasFactory;

    private const SCALAR_TYPES = [
        'String',
        'Integer',
        'Float',
        'Boolean',
        'Text',
        'Wiki',
    ];

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

    public function getColumnNameAttribute(): string
    {
        return Str::snake($this->id);
    }

    public function getTableNameAttribute(): string
    {
        return 'pivots.' . Str::snake($this->id);
    }

    public function build(): void
    {
        if ($this->type['name'] === 'List') {

            if (Schema::hasTable($this->tableName)) {
                return;
            }

            if ($this->type['of']['name'] === 'Enum') {

                Schema::create($this->tableName, function (Blueprint $table) {
                    $table->uuid($this->section->id);
                    $table->uuid($this->columnName);

                    $table->foreign($this->section->id)
                        ->references('id')
                        ->on($this->section->tableName);

                    $table->foreign($this->columnName)
                        ->references('id')
                        ->on('enum_values');
                });
            }

            if ($this->type['of']['name'] === 'Dictionary') {
                Schema::create($this->tableName, function (Blueprint $table) {
                    $table->uuid($this->section->id);
                    $table->uuid($this->columnName);

                    $table->foreign($this->section->id)
                        ->references('id')
                        ->on($this->section->tableName);

                    $table->foreign($this->columnName)
                        ->references('id')
                        ->on(Section::findOrFail($this->type['of']['of'])->tableName);
                });
            }

            if ($this->type['of']['name'] === 'File') {

                Schema::create($this->tableName, function (Blueprint $table) {
                    $table->uuid($this->section->id);
                    $table->uuid($this->columnName);

                    $table->foreign($this->section->id)
                        ->references('id')
                        ->on($this->section->tableName);

                    $table->foreign($this->columnName)
                        ->references('id')
                        ->on('files');
                });
            }

            return;
        }

        if (Schema::hasColumn($this->section->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->section->tableName, function (Blueprint $table) {
            if ($this->type['name'] === 'String') {
                $table->string($this->columnName)->nullable();
            }

            if ($this->type['name'] === 'File') {
                $table->uuid($this->columnName)->nullable();
                $table->foreign($this->columnName, $this->id)->references('id')->on('files');
            }

            if ($this->type['name'] === 'Integer') {
                $table->integer($this->columnName)->nullable();
            }

            if ($this->type['name'] === 'Float') {
                $table->float($this->columnName)->nullable();
            }

            if ($this->type['name'] === 'Boolean') {
                $table->boolean($this->columnName)->nullable();
            }

            if ($this->type['name'] === 'Text') {
                $table->text($this->columnName)->nullable();
            }

            if ($this->type['name'] === 'Wiki') {
                $table->text($this->columnName)->nullable();
            }

            if ($this->type['name'] === 'Enum') {
                $table->uuid($this->columnName)->nullable();
                $table->foreign($this->columnName, $this->id)->references('id')->on('enum_values');
            }

            if ($this->type['name'] === 'Dictionary') {
                $table->uuid($this->columnName)->nullable();
                $table->foreign($this->columnName)->references('id')->on($this->type['of']);
            }
        });
    }

    public function drop(): void
    {
        if ($this->type['name'] === 'List') {
            Schema::dropIfExists($this->tableName);
            return;
        }

        Schema::table($this->section->tableName, function (Blueprint $table) {
            $table->dropColumn($this->columnName);
        });
    }
}
