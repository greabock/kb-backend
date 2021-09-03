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
            if ($field->type['of']['name'] === FieldType::T_SELECT) {
                Schema::table($field->section->table_name, function (Blueprint $table) use ($field) {
                    $table->jsonb($field->id)->nullable();
                });

                return;
            }

            if (Schema::hasTable($field->pivot_name)) {
                return;
            }

            if ($field->type['of']['name'] === FieldType::T_ENUM) {

                Schema::create($field->pivot_name, function (Blueprint $table) use ($field) {
                    $table->uuid($field->local_pivot_key);
                    $table->uuid($field->foreign_key);

                    $table->foreign($field->local_pivot_key)
                        ->references('id')
                        ->on($field->section->table_name);

                    $table->foreign($field->foreign_key)
                        ->references('id')
                        ->on('enum_values');
                });

                return;
            }

            if ($field->type['of']['name'] === FieldType::T_DICTIONARY) {
                Schema::create($field->pivot_name, function (Blueprint $table) use ($field) {
                    $table->uuid($field->local_pivot_key);
                    $table->uuid($field->foreign_key);

                    $table->foreign($field->local_pivot_key)
                        ->references('id')
                        ->on($field->section->table_name);

                    $table->foreign($field->foreign_key)
                        ->references('id')
                        ->on(Section::findOrFail($field->type['of']['of'])->table_name);
                });

                return;
            }

            if ($field->type['of']['name'] === FieldType::T_FILE) {

                Schema::create($field->pivot_name, function (Blueprint $table) use ($field) {
                    $table->uuid($field->local_pivot_key);
                    $table->uuid($field->foreign_key);

                    $table->foreign($field->local_pivot_key)
                        ->references('id')
                        ->on($field->section->table_name);

                    $table->foreign($field->foreign_key)
                        ->references('id')
                        ->on('files');
                });

                return;
            }

            return;
        }

        if (Schema::hasColumn($field->section->table_name, $field->id)) {
            return;
        }

        Schema::table($field->section->table_name, function (Blueprint $table) use ($field) {
            if ($field->type['name'] === FieldType::T_SELECT) {
                $table->string($field->id)->nullable();
            }

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
                $table->uuid($field->foreign_key)->nullable();
                $table->foreign($field->foreign_key, $field->id)->references('id')->on('files');
            }

            if ($field->type['name'] === FieldType::T_ENUM) {
                $table->uuid($field->foreign_key)->nullable();
                $table->foreign($field->foreign_key, $field->id)->references('id')->on('enum_values');
            }

            if ($field->type['name'] === FieldType::T_DICTIONARY) {
                $table->uuid($field->foreign_key)->nullable();
                $table->foreign($field->foreign_key)->references('id')->on($field->type['of']);
            }

        });
    }

    public function drop(Field $field): void
    {
        if ($field->type['name'] === FieldType::T_LIST) {
            if ($field->type['of']['name'] === FieldType::T_SELECT) {
                Schema::table($field->section->table_name, function (Blueprint $table) use ($field) {
                    if (Schema::hasColumn($field->section->table_name, $field->id)) {
                        $table->dropColumn($field->id);
                    }
                });

                return;
            }

            Schema::dropIfExists($field->pivot_name);

            return;
        }

        Schema::table($field->section->table_name, function (Blueprint $table) use ($field) {
            if (Schema::hasColumn($field->section->table_name, $field->id)) {
                $table->dropColumn($field->id);
            }
        });
    }
}
