<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_blogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cate_name');
            $table->string('cate_desc')->nullable();
            $table->integer('cate_parent')->unsigned();
            $table->boolean('cate_active');
            $table->mediumInteger('cate_order');
            $table->string('cate_option')->nullable();
            $table->string('cate_keyword')->nullable();
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
        Schema::dropIfExists('category_blogs');
    }
}
