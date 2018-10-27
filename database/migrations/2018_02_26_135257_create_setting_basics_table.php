<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingBasicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_basics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->boolean('hide_all_cmt')->default(false);
            $table->boolean('hide_cmt_has_phone')->default(false);
            $table->boolean('hide_cmt_has_key')->default(false);
            $table->mediumText('key_cmt_hide')->nullable();
            $table->boolean('auto_like')->default(false);
            $table->boolean('ping_notify')->default(false);
            $table->boolean('priority_cmt_has_key')->default(false);
            $table->mediumText('key_cmt_priority')->nullable();
            $table->boolean('filter_email')->default(false);
            $table->boolean('filter_phone')->default(false);
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
        Schema::dropIfExists('setting_basics');
    }
}
