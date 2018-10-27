<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailFacebookToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_fb_email')->nullable();
            $table->integer('user_time_expire')->nullable();
            $table->boolean('blocked')->default(false);
            $table->integer('time_expire_blocked')->nullable();
            $table->boolean('destroyed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('user_fb_email');
            $table->dropColumn('user_time_expire');
            $table->dropColumn('blocked');
            $table->dropColumn('time_expire_blocked');
            $table->dropColumn('destroyed');
        });
    }
}
