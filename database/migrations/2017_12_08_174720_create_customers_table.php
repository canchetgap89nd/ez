<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->bigInteger('fb_id_cus')->unsigned()->nullable();
            $table->string('name_cus');
            $table->string('phone_cus')->nullable();
            $table->timestamp('birthday_cus')->nullable();
            $table->string('gender_cus')->nullable();
            $table->string('email_cus')->nullable();
            $table->string('address_cus')->nullable();
            $table->boolean('banned')->default(false);
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
        Schema::dropIfExists('customers');
    }
}
