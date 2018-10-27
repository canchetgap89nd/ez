<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeStartAutoReplyInSettingBasicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_basics', function(Blueprint $table) {
            $table->boolean('has_time_inbox')->default(false);
            $table->boolean('has_time_comment')->default(false);
            $table->time('time_start_comment')->nullable();
            $table->time('time_end_comment')->nullable();
            $table->time('time_start_inbox')->nullable();
            $table->time('time_end_inbox')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_basics', function(Blueprint $table) {
            $table->dropColumn('has_time_inbox');
            $table->dropColumn('has_time_comment');
            $table->dropColumn('time_start_comment');
            $table->dropColumn('time_end_comment');
            $table->dropColumn('time_start_inbox');
            $table->dropColumn('time_end_inbox');
        });
    }
}
