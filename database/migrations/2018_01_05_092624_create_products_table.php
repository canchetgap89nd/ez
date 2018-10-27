<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prod_code', 50);
            $table->string('prod_name');
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('user_id')->unsigned();
            $table->decimal('prod_price', 19, 4)->unsigned();
            $table->decimal('prod_price_imp', 19, 4)->unsigned();
            $table->integer('prod_quantity');
            $table->string('prod_thumb', 500);
            $table->integer('count_childs')->unsigned()->default(0);
            $table->string('properties')->nullable();
            $table->integer('count_sold')->unsigned()->default(0);
            $table->string('status', 50);
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
        Schema::dropIfExists('products');
    }
}
