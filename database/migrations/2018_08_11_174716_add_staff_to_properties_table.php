<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaffToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('staff_id')->unsigned();
        });

        Schema::table('property_values', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
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
        Schema::table('properties', function($table) {
            $table->dropColumn('staff_id');
        });

        Schema::table('property_values', function($table) {
            $table->dropColumn('user_id');
            $table->dropColumn('staff_id');
        });
    }
}
