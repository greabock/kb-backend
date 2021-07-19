<?php

declare(strict_types=1);

namespace App\Services;

use Schema;
use App\Models\Section;
use App\Models\Section\Field;
use Illuminate\Database\Schema\Blueprint;

class ColumnBuilder
{
    public function build(Field $field): void
    {
        if ($field->type['name'] === 'List') {

            if (Schema::hasTable($field->pivotName)) {
                return;
            }

            if ($field->type['of']['name'] === 'Enum') {

                Schema::create($field->pivotName, function (Blueprint $table) use ($field) {
                    $table->uuid($field->localPivotKey);
                    $table->uuid($field->foreignKey);

                    $table->foreign($field->localPivotKey)
                        ->references('id')
                        ->on($field->section->tableName);

                    $table->foreign($field->foreignKey)
                        ->references('id')
                        ->on('enum_values');
                });
            }

            if ($field->type['of']['name'] === 'Dictionary') {
                Schema::create($field->pivotName, function (Blueprint $table) use ($field) {
                    $table->uuid($field->localPivotKey);
                    $table->uuid($field->foreignKey);

                    $table->foreign($field->localPivotKey)
                        ->references('id')
                        ->on($field->section->tableName);

                    $table->foreign($field->foreignKey)
                        ->references('id')
                        ->on(Section::findOrFail($field->type['of']['of'])->tableName);
                });
            }

            if ($field->type['of']['name'] === 'File') {

                Schema::create($field->pivotName, function (Blueprint $table) use ($field) {
                    $table->uuid($field->localPivotKey);
                    $table->uuid($field->foreignKey);

                    $table->foreign($field->localPivotKey)
                        ->references('id')
                        ->on($field->section->tableName);

                    $table->foreign($field->foreignKey)
                        ->references('id')
                        ->on('files');
                });
            }

            return;
        }

        if (Schema::hasColumn($field->section->tableName, $field->id)) {
            return;
        }

        Schema::table($field->section->tableName, function (Blueprint $table) use ($field) {

            if ($field->type['name'] === 'String') {
                $table->string($field->id)->nullable();
            }

            if ($field->type['name'] === 'Integer') {
                $table->integer($field->id)->nullable();
            }

            if ($field->type['name'] === 'Float') {
                $table->float($field->id)->nullable();
            }

            if ($field->type['name'] === 'Boolean') {
                $table->boolean($field->id)->nullable();
            }

            if ($field->type['name'] === 'Text') {
                $table->text($field->id)->nullable();
            }

            if ($field->type['name'] === 'Wiki') {
                $table->text($field->id)->nullable();
            }

            if ($field->type['name'] === 'File') {
                $table->uuid($field->foreignKey)->nullable();
                $table->foreign($field->foreignKey, $field->id)->references('id')->on('files');
            }

            if ($field->type['name'] === 'Enum') {
                $table->uuid($field->foreignKey)->nullable();
                $table->foreign($field->foreignKey, $field->id)->references('id')->on('enum_values');
            }

            if ($field->type['name'] === 'Dictionary') {
                $table->uuid($field->foreignKey)->nullable();
                $table->foreign($field->foreignKey)->references('id')->on($field->type['of']);
            }
        });
    }

    public function drop(Field $field): void
    {
        if ($field->type['name'] === 'List') {
            Schema::dropIfExists($field->pivotName);
            return;
        }

        Schema::table($field->section->tableName, function (Blueprint $table) use ($field) {
            $table->dropColumn($field->columnName);
        });
    }
}
