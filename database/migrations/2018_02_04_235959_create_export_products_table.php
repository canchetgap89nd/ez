<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('export_code', 50);
            $table->integer('user_id')->unsigned();
            $table->integer('quantity_ex')->unsigned();
            $table->integer('inventory_ex')->unsigned();
            $table->decimal('amount_ex', 19, 4)->unsigned();
            $table->string('status_ex', 50);
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
        Schema::dropIfExists('export_products');
    }
}
