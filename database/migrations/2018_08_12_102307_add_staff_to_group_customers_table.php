<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaffToGroupCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_customers', function (Blueprint $table) {
            $table->integer('staff_id')->unsigned();
        });

        Schema::table('setting_basics', function (Blueprint $table) {
            $table->integer('staff_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_customers', function($table) {
            $table->dropColumn('staff_id');
        });

        Schema::table('setting_basics', function($table) {
            $table->dropColumn('staff_id');
        });
    }
}
