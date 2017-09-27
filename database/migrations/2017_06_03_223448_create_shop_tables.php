<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('shop_categories', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name', 50);
        $table->integer('order')->unsigned();
        $table->boolean('displayed')->default(0);
        $table->timestamps();
      });
      Schema::create('shop_items', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name', 150);
        $table->text('description');
        $table->integer('category_id')->unsigned();
        $table->foreign('category_id')->references('id')->on('shop_categories');
        $table->float('price');
        $table->boolean('displayed')->default(0);
        $table->text('commands')->nullable()->default(null);
        $table->string('image_path')->nullable()->default(null);
        $table->boolean('need_connected')->default(true);
        $table->timestamps();
      });
      Schema::create('shop_items_abilities', function (Blueprint $table) {
        $table->increments('id');
        $table->string('model'); // model name
        $table->integer('item_id')->unsigned();
        $table->foreign('item_id')->references('id')->on('shop_items');
        $table->integer('condition_max')->unsigned()->nullable()->default(null);
        $table->timestamps();
      });
      Schema::create('shop_ranks', function (Blueprint $table) {
        $table->increments('id');
        $table->text('advantages');
        $table->string('slug', 30);
        $table->integer('item_id')->unsigned();
        $table->foreign('item_id')->references('id')->on('shop_items');
        $table->timestamps();
      });
      Schema::create('shop_items_purchase_histories', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->foreign('user_id')->references('id')->on('users');
        $table->integer('item_id')->unsigned();
        $table->foreign('item_id')->references('id')->on('shop_items');
        $table->integer('quantity')->unsigned()->default(1);
        $table->ipAddress('ip');
        $table->timestamps();
      });
      Schema::create('shop_sales', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('product_id')->unsigned()->nullable()->default(null);
        $table->string('product_type', 8); // ITEM or CATEGORY
        $table->float('reduction'); // percentage
        $table->dateTime('deleted_at')->nullable(); // expire date
        $table->timestamps();
      });
      Schema::create('shop_sale_histories', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->foreign('user_id')->references('id')->on('users');
        $table->integer('item_id')->unsigned();
        $table->foreign('item_id')->references('id')->on('shop_items');
        $table->integer('sale_id')->unsigned();
        $table->foreign('sale_id')->references('id')->on('shop_sales');
        $table->integer('history_id')->unsigned();
        $table->foreign('history_id')->references('id')->on('shop_items_purchase_histories');
        $table->float('reduction');
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
      Schema::dropIfExists('shop_categories');
      Schema::dropIfExists('shop_items');
      Schema::dropIfExists('shop_items_abilities');
      Schema::dropIfExists('shop_ranks');
      Schema::dropIfExists('shop_items_purchase_histories');
      Schema::dropIfExists('shop_sales');
      Schema::dropIfExists('shop_sale_histories');
    }
}