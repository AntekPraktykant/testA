<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('name');
            $table->string('size');
            $table->double('price');
            $table->integer('beer_id');
            $table->string('image_url');
            $table->string('category');
            $table->string('abv');
            $table->string('style');
            $table->string('attributes');
            $table->string('type');
            $table->unsignedInteger('brewer_id');
            $table->string('country');
            $table->boolean('on_sale');
            //$table->integer('2xbeer_id') -> 1000;
            $table->foreign('brewer_id')->references('id')->on('brewers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beers');
    }
}
