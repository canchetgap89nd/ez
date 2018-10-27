<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistic_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('page_id')->unsigned()->nullable();
            $table->integer('order_id')->unsigned();
            $table->double('sale_amount')->nullable();
            $table->double('back_amount')->nullable();
            $table->double('discount')->nullable();
            $table->double('revenue')->nullable();
            $table->double('origin_val')->nullable();
            $table->double('profit')->nullable();
            $table->double('ship_fee')->nullable();
            $table->integer('action_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistic_sales');
    }
}
