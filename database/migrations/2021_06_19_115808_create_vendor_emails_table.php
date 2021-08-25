<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_emails', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code',100)->nullable();
            $table->string('vendor_name',255)->nullable();
            $table->string('sales_emp',255)->nullable(); 
            $table->string('email',255)->nullable(); 
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
        Schema::dropIfExists('vendor_emails');
    }
}
