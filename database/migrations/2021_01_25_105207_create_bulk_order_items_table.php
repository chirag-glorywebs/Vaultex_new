<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_order_items', function (Blueprint $table) {
            $table->id('boi_id');
            $table->bigInteger('order_id')->unsigned();
            $table->string('product_or_category_details',150)->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->string('brand',100)->nullable();
            $table->string('status',50)->nullable();
            //$table->foreign('order_id')->references('id')->on('bulk_orders')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('bulk_order_items');
    }
}
