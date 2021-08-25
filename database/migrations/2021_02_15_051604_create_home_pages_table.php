<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->string('offer_top_1',300)->nullable();
            $table->string('offer_top_1_url',100)->nullable();
            $table->string('offer_top_2',300)->nullable();
            $table->string('offer_top_2_url',100)->nullable();
            $table->string('offer_top_3',300)->nullable();
            $table->string('offer_top_3_url',100)->nullable();
            $table->string('offer_bottom_1',300)->nullable();
            $table->string('offer_bottom_1_url',100)->nullable();
            $table->text('offer_bottom_1_contents')->nullable();
            $table->string('offer_bottom_2',300)->nullable();
            $table->string('offer_bottom_2_url',100)->nullable();
            $table->text('offer_bottom_2_contents')->nullable();
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
        Schema::dropIfExists('home_pages');
    }
}
