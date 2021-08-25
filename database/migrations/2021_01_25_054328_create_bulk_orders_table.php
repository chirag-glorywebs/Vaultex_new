<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_orders', function (Blueprint $table) {
            $table->id();
            $table->string('gstin',50)->nullable();
            $table->string('name',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('phone',50)->nullable();
            $table->text('description')->nullable();
            $table->string('user_id')->nullable();
            $table->integer('business_or_customer')->default(0);

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
        Schema::dropIfExists('bulk_orders');
    }
}
