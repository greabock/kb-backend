<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftedeltesToEnumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enums', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('enum_values', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('section_fields', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enums', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('enum_values', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('section_fields', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
