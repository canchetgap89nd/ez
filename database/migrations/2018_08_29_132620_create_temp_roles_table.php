<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_parent')->unsigned();
            $table->string('user_name_parent')->nullable();
            $table->bigInteger('fb_user_parent')->unsigned();
            $table->integer('user_staff')->unsigned()->nullable();
            $table->string('user_name_staff')->nullable();
            $table->bigInteger('fb_user_staff')->unsigned();
            $table->bigInteger('fb_page_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->string('page_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_roles');
    }
}
