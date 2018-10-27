<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        for ($i=0; $i < 100; $i++) {
            Schema::create('message_' . $i, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('conver_id')->unsigned();
                $table->bigInteger('from_id')->unsigned();
                $table->string('fb_message_id', 100);
                $table->string('from_name');
                $table->text('message')->nullable();
                $table->boolean('has_phone')->default(false);
                $table->boolean('has_key')->default(false);
                $table->boolean('unread')->default(false);
                $table->integer('staff_reply_id')->unsigned()->nullable();
                $table->bigInteger('fb_page_id')->unsigned();
                $table->string('from_platform', 20)->nullable();
                $table->integer('created_time');
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
            Schema::dropIfExists('message_' . $i);
        }
    }
}
