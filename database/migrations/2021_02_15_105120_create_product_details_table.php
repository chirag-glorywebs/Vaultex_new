<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->string('QryGroup1', '255')->nullable();
            $table->string('QryGroup2', '255')->nullable();
            $table->string('QryGroup3', '255')->nullable();
            $table->string('QryGroup4', '255')->nullable();
            $table->string('QryGroup5', '255')->nullable();
            $table->string('SizeCd', '255')->nullable();
            $table->string('SizeName', '255')->nullable();
            $table->string('U_SCartQty', '255')->nullable();
            $table->string('CBM', '255')->nullable();
            $table->string('OnHand', '255')->nullable();
            $table->string('U_Itemgrp', '255')->nullable();
            $table->string('U_Itemgrpname', '255')->nullable();
            $table->string('U_OrgCountCod', '255')->nullable();
            $table->string('FirmCode', '255')->nullable();
            $table->string('FirmName', '255')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_details');
    }
}
