<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaffAndFbpageidToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('staff_id')->unsigned();
            $table->bigInteger('fb_page_id')->unsigned();
        });

        Schema::table('customer_pages', function (Blueprint $table) {
            $table->bigInteger('fb_page_id')->unsigned();
        });

        Schema::table('customer_notes', function (Blueprint $table) {
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
        Schema::table('customers', function($table) {
            $table->dropColumn('fb_page_id');
            $table->dropColumn('staff_id');
        });

        Schema::table('customer_pages', function($table) {
            $table->dropColumn('fb_page_id');
        });

        Schema::table('customer_notes', function($table) {
            $table->dropColumn('staff_id');
        });
    }
}
