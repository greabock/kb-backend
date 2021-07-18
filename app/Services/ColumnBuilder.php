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

            if (Schema::hasTable($field->tableName)) {
                return;
            }

            if ($field->type['of']['name'] === 'Enum') {

                Schema::create($field->tableName, function (Blueprint $table) use ($field) {
                    $table->uuid($field->getLocalPivotKeyAttribute());
                    $table->uuid($field->getForeignKeyAttibute());

                    $table->foreign($field->getLocalPivotKeyAttribute())
                        ->references('id')
                        ->on($field->section->tableName);

                    $table->foreign($field->getForeignKeyAttibute())
                        ->references('id')
                        ->on('enum_values');
                });
            }

            if ($field->type['of']['name'] === 'Dictionary') {
                Schema::create($field->tableName, function (Blueprint $table) use ($field) {
                    $table->uuid($field->getLocalPivotKeyAttribute());
                    $table->uuid($field->getForeignKeyAttibute());

                    $table->foreign($field->getLocalPivotKeyAttribute())
                        ->references('id')
                        ->on($field->section->tableName);

                    $table->foreign($field->getForeignKeyAttibute())
                        ->references('id')
                        ->on(Section::findOrFail($field->type['of']['of'])->tableName);
                });
            }

            if ($field->type['of']['name'] === 'File') {

                Schema::create($field->tableName, function (Blueprint $table) use ($field) {
                    $table->uuid($field->getLocalPivotKeyAttribute());
                    $table->uuid($field->getForeignKeyAttibute());

                    $table->foreign($field->getLocalPivotKeyAttribute())
                        ->references('id')
                        ->on($field->section->tableName);

                    $table->foreign($field->getForeignKeyAttibute())
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

            if ($field->type['name'] === 'File') {
                $table->uuid($field->id)->nullable();
                $table->foreign($field->id, $field->id)->references('id')->on('files');
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

            if ($field->type['name'] === 'Enum') {
                $table->uuid($field->getRelationColumnName())->nullable();
                $table->foreign($field->getRelationColumnName(), $field->id)->references('id')->on('enum_values');
            }

            if ($field->type['name'] === 'Dictionary') {
                $table->uuid($field->id)->nullable();
                $table->foreign($field->id)->references('id')->on($field->type['of']);
            }
        });
    }

    public function drop(Field $field): void
    {
        if ($field->type['name'] === 'List') {
            Schema::dropIfExists($field->tableName);
            return;
        }

        Schema::table($field->section->tableName, function (Blueprint $table) use ($field) {
            $table->dropColumn($field->columnName);
        });
    }
}
