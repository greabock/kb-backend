<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIsFilterableColumnFromSectionFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_fields', function (Blueprint $table) {
            $table->dropColumn('is_filterable');
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
            $table->boolean('is_filterable')->default(false);
        });
    }
}
