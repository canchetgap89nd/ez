<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_imports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('import_id')->unsigned();
            $table->integer('prod_id')->unsigned();
            $table->string('prod_code');
            $table->string('prod_name');
            $table->string('properties')->nullable();
            $table->integer('quantity_prod')->unsigned();
            $table->decimal('price_imp', 19, 4);
            $table->integer('inventory_prod');
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
        Schema::dropIfExists('products_imports');
    }
}
