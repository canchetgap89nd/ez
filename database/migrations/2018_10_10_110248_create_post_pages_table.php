<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->bigInteger('fb_page_id')->unsigned();
            $table->string('fb_page_name');
            $table->string('fb_post_id', 50);
            $table->string('message')->nullable();
            $table->string('picture', 200)->nullable();
            $table->integer('created_time')->unsigned();
            $table->integer('updated_time')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_pages');
    }
}
