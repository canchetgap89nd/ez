<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        for ($i=0; $i < 100; $i++) { 
            Schema::create('comment_' . $i, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->bigInteger('fb_page_id')->unsigned();
                $table->integer('conver_id')->unsigned();
                $table->string('fb_comment_id', 50);
                $table->integer('parent_id')->unsigned()->nullable();
                $table->bigInteger('from_id')->unsigned();
                $table->string('from_name');
                $table->text('message')->nullable();
                $table->boolean('can_reply_privately')->default(true);
                $table->boolean('is_hidden')->default(true);
                $table->boolean('is_remove')->default(true);
                $table->boolean('user_likes')->default(true);
                $table->boolean('can_hide')->default(true);
                $table->boolean('can_like')->default(true);
                $table->boolean('can_comment')->default(true);
                $table->boolean('can_remove')->default(true);
                $table->boolean('has_phone')->default(false);
                $table->boolean('has_key')->default(false);
                $table->boolean('unread')->default(false);
                $table->integer('created_time')->unsigned();
                $table->integer('staff_reply_id')->unsigned()->nullable();
                $table->integer('post_id');
                $table->string('from_platform', 20)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        for ($i=0; $i < 100; $i++) { 
            Schema::dropIfExists('comment_' . $i);
        }
    }
}
