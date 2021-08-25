<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantCombinations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_combinations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
          /*   $table->bigInteger('product_variant_id')->unsigned();
            $table->foreign('product_variant_id')->references('id')->on('product_attributes'); */
            $table->text('product_variant_data')->nullable();
            $table->integer('variation_id1')->nullable();
            $table->integer('variation_id2')->nullable();
            $table->string('sku',50)->nullable();
            $table->string('item_code',50)->nullable();
            $table->string('item_name',255)->nullable();
            $table->integer('OnHand')->nullable();
            $table->integer('IsCommited')->nullable();
            $table->integer('OnOrder')->nullable();
            $table->decimal('U_GrossWt', 5, 2)->nullable();
            $table->decimal('U_NetWt', 5, 2)->nullable();     
            $table->timestamps();
            $table->softDeletes();  
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variant_combinations');
    }
}
