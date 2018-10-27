<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaffToImportProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_products', function (Blueprint $table) {
            $table->integer('staff_id')->unsigned();
        });
        Schema::table('export_products', function (Blueprint $table) {
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
        Schema::table('import_products', function($table) {
            $table->dropColumn('staff_id');
        });
        Schema::table('export_products', function($table) {
            $table->dropColumn('staff_id');
        });
    }
}
