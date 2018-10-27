<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoReplyToSettingBasicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_basics', function (Blueprint $table) {
            $table->boolean('auto_comment')->default(false);
            $table->boolean('auto_inbox')->default(false);
            $table->string('content_comment')->nullable();
            $table->string('content_inbox')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_basics', function($table) {
            $table->dropColumn('auto_comment');
            $table->dropColumn('auto_inbox');
            $table->dropColumn('content_comment');
            $table->dropColumn('content_inbox');
        });
    }
}
