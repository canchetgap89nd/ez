<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVietNamWardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viet_nam_wards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_ward');
            $table->string('type_ward');
            $table->string('location_ward');
            $table->integer('district_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viet_nam_wards');
    }
}
