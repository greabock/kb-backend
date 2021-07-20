<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFilterableColumnToSectionFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_fields', function (Blueprint $table) {
            $table->boolean('is_filterable');
            $table->renameColumn('use_in_card', 'is_present_in_card');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_fields', function (Blueprint $table) {
            $table->dropColumn('is_filterable');
            $table->renameColumn( 'is_present_in_card', 'use_in_card');
        });
    }
}
