<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalSchemas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(\DB::raw("CREATE SCHEMA IF NOT EXISTS sections"));
        DB::statement(\DB::raw("CREATE SCHEMA IF NOT EXISTS pivots"));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(\DB::raw("DROP SCHEMA IF EXISTS pivots cascade"));
        DB::statement(\DB::raw("DROP SCHEMA IF EXISTS sections cascade"));
    }
}
