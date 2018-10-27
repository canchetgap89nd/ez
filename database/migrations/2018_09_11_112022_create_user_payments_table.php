<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pay_code', 50)->unique();
            $table->integer('user_id')->unsigned();
            $table->double('price')->unsigned();
            $table->double('amount')->unsigned();
            $table->double('tax')->unsigned()->default(0);
            $table->double('other_payment')->unsigned()->default(0);
            $table->double('discount')->unsigned()->default(0);
            $table->double('total_payable')->unsigned();
            $table->double('total_after_discount')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->integer('duration')->unsigned();
            $table->integer('duration_bonus')->unsigned()->default(0);
            $table->boolean('paid')->default(false);
            $table->integer('admin_id')->unsigned()->nullable();
            $table->boolean('is_active')->default(false);
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
        Schema::dropIfExists('user_payments');
    }
}
