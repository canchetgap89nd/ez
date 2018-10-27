<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_exports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('export_id')->unsigned();
            $table->integer('prod_id')->unsigned();
            $table->string('prod_code');
            $table->string('prod_name');
            $table->string('properties')->nullable();
            $table->integer('quantity_ex')->unsigned();
            $table->decimal('price_ex', 19, 4)->unsigned();
            $table->integer('inventory_ex')->unsigned();
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
        Schema::dropIfExists('product_exports');
    }
}
