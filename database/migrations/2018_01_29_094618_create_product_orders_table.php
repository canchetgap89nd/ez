<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('prod_id')->unsigned();
            $table->string('prod_code');
            $table->string('prod_name');
            $table->string('properties')->nullable();
            $table->integer('order_id')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('price', 19, 4)->unsigned();
            $table->decimal('price_sale', 19, 4)->unsigned();
            $table->integer('camp_id')->unsigned()->nullable();
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
        Schema::dropIfExists('product_orders');
    }
}
