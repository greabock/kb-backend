<?php

declare(strict_types=1);

namespace App\Services;

use Schema;
use App\Models\Section;
use App\Models\Section\Field;
use App\Validation\Rules\FieldType;
use Illuminate\Database\Schema\Blueprint;

class ColumnBuilder
{
    public function build(Field $field): void
    {
        if ($field->type['name'] === FieldType::T_LIST) {

            if (Schema::hasTable($field->pivotName)) {
                return;
            }

            if ($field->type['of']['name'] === FieldType::T_ENUM) {

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

            if ($field->type['of']['name'] === FieldType::T_DICTIONARY) {
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

            if ($field->type['of']['name'] === FieldType::T_FILE) {

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

            if ($field->type['name'] === FieldType::T_STRING) {
                $table->string($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_INTEGER) {
                $table->integer($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_FLOAT) {
                $table->float($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_BOOLEAN) {
                $table->boolean($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_TEXT) {
                $table->text($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_WIKI) {
                $table->text($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_DATE) {
                $table->timestamp($field->id)->nullable();
            }

            if ($field->type['name'] === FieldType::T_FILE) {
                $table->uuid($field->foreignKey)->nullable();
                $table->foreign($field->foreignKey, $field->id)->references('id')->on('files');
            }

            if ($field->type['name'] === FieldType::T_ENUM) {
                $table->uuid($field->foreignKey)->nullable();
                $table->foreign($field->foreignKey, $field->id)->references('id')->on('enum_values');
            }

            if ($field->type['name'] === FieldType::T_DICTIONARY) {
                $table->uuid($field->foreignKey)->nullable();
                $table->foreign($field->foreignKey)->references('id')->on($field->type['of']);
            }
        });
    }

    public function drop(Field $field): void
    {
        if ($field->type['name'] === FieldType::T_LIST) {
            Schema::dropIfExists($field->pivotName);
            return;
        }

        Schema::table($field->section->tableName, function (Blueprint $table) use ($field) {
            $table->dropColumn($field->columnName);
        });
    }
}
