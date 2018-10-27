<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleAttachTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        for ($i=0; $i < 100; $i++) { 
            Schema::create('attachment_' . $i, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('entity_id')->unsigned();
                $table->string('url', 200)->nullable();
                $table->string('preview_url', 200)->nullable();
                $table->string('file_url', 200)->nullable();
                $table->tinyInteger('entity_type')->unsigned();
                $table->string('type', 20);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        for ($i=0; $i < 100; $i++) { 
            Schema::dropIfExists('attachment_' . $i);
        }
    }
}
