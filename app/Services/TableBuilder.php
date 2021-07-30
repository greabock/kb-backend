<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Section;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class TableBuilder
{
    public function __construct(
        private ColumnBuilder $columnBuilder
    )
    {
    }

    public function create(Section $section): void
    {
        Schema::create($section->tableName, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        $section->fields->each(fn(Section\Field $field) => $this->columnBuilder->build($field));
    }

    public function drop(Section $section): void
    {
        Schema::dropIfExists($section->tableName);
    }

}
