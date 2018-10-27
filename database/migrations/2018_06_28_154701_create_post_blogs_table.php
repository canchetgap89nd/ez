<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_blogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_title');
            $table->integer('auth_id')->unsigned();
            $table->mediumText('post_desc');
            $table->string('post_keyword')->nullable();
            $table->mediumText('post_content');
            $table->longText('post_thumb');
            $table->dateTime('post_time_schedule')->nullable();
            $table->integer('post_cate1')->unsigned();
            $table->integer('post_cate2')->unsigned();
            $table->integer('views')->unsigned()->default(0);
            $table->boolean('is_draft');
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
        Schema::dropIfExists('post_blogs');
    }
}
