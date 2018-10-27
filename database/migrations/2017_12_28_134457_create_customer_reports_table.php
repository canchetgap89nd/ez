<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user_reported')->unsigned();
            $table->string('name_cus', 50)->nullable();
            $table->string('phone_cus', 50)->nullable();
            $table->string('email_cus', 100)->nullable();
            $table->bigInteger('fb_id_cus')->nullable();
            $table->string('title_report', 100);
            $table->string('des_report');
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
        Schema::dropIfExists('customer_reports');
    }
}
