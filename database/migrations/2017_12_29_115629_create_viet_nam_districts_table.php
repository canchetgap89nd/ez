<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVietNamDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viet_nam_districts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_district');
            $table->string('type_district');
            $table->string('location_district');
            $table->integer('province_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viet_nam_districts');
    }
}
