<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code', 50);
            $table->integer('user_id')->unsigned();
            $table->integer('cus_id')->unsigned();
            $table->integer('page_id')->unsigned()->nullable();
            $table->string('name_receive');
            $table->string('phone_receive', 50)->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->string('ad_receive')->nullable();
            $table->string('email_receive')->nullable();
            $table->decimal('discount', 19, 4)->unsigned()->default(0);
            $table->decimal('ship_fee', 19, 4)->unsigned()->default(0);
            $table->decimal('other_fee', 19, 4)->unsigned()->default(0);
            $table->decimal('total_value', 19, 4)->unsigned()->default(0);
            $table->decimal('value_has_sale', 19, 4)->unsigned()->default(0);
            $table->decimal('total_amount', 19, 4)->unsigned()->default(0);
            $table->string('note_order')->nullable();
            $table->decimal('total_pay', 19, 4)->unsigned()->default(0);
            $table->string('status_order', 50);
            $table->timestamp('deadline_order')->nullable();
            $table->timestamp('time_confirmed')->nullable();
            $table->timestamp('time_sending')->nullable();
            $table->timestamp('time_sent')->nullable();
            $table->timestamp('time_refunding')->nullable();
            $table->timestamp('time_refunded')->nullable();
            $table->timestamp('time_canceled')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
