<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations. 
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 191)->nullable();
            $table->text('description',16383)->nullable();
            $table->string('discount_type', 100)->comment('Options: fixed_cart, percent, fixed_product and percent_product. Default: fixed_cart.')->default('fixed_cart');
            $table->integer('amount')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('expiry_date');
            $table->integer('usage_count')->nullable();
            $table->integer('usage_limit')->nullable();
			$table->integer('usage_limit_per_user')->nullable();
			$table->decimal('minimum_amount', 10)->nullable();
			$table->text('email_restrictions', 16383)->nullable();
            $table->string('used_by', 191)->nullable();
            $table->boolean('status')->default(1);
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('coupons');
    }
}
