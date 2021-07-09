<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enum_values', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('title');
            $table->uuid('enum_id');
            $table->timestamps();

            $table->foreign('enum_id')->references('id')->on('enums');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_values');
    }
}
