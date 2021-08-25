<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->integer('category_id')->nullable()->default(0);
            $table->integer('brand_id')->nullable()->default(0);
            $table->string('sku');
            $table->string('main_image');
            $table->integer('regular_price');
            $table->integer('sale_price');
            $table->integer('inventory');
            $table->text('specification')->nullable();
            $table->text('tech_documents')->nullable();
            $table->string('video')->nullable();
            $table->text('gallery')->nullable();
            $table->string('slug',191)->nullable()->index();
            $table->text('threesixty_images')->nullable();
            $table->string('download_datasheet')->nullable();
            $table->string('att_ids')->nullable()->default(0);
            $table->string('att_variations')->nullable()->default(0);
            $table->integer('userid')->nullable()->default(0);
            $table->string('seo_name')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_keyword')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->integer('status')->default(0);
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
        //
    }
}
