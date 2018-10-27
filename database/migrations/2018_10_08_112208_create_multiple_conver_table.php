<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleConverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        for ($i=0; $i < 100; $i++) { 
            Schema::create('conver_' . $i, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->bigInteger('from_id')->unsigned();
                $table->string('from_name');
                $table->bigInteger('fb_page_id')->unsigned();
                $table->string('thread_id', 100);
                $table->string('last_message')->nullable();
                $table->tinyInteger('type')->unsigned();
                // $table->integer('customer_id')->unsigned()->nullable();
                $table->integer('updated_time');
                $table->string('post_id', 100)->nullable();
                $table->boolean('unreply')->default(false);
                $table->boolean('has_phone')->default(false);
                $table->boolean('unread')->default(false);
                $table->boolean('has_order')->default(false);
                $table->boolean('has_note')->default(false);
                $table->boolean('has_key')->default(false);
                $table->boolean('is_multiple_chat')->default(false);
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
            Schema::dropIfExists('conver_' . $i);
        }
    }
}
