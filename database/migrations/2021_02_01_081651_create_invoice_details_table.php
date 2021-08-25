<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->integer('DocNum');
            $table->string('CardCode');
            $table->string('CardName');
            $table->string('EmpName');
            $table->string('Telephone');
            $table->string('USR_Moile');
            $table->string('USR_Email');
            $table->string('Place_Of_Supply');
            $table->string('DocDate');
            $table->string('DocDueDate');
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
        Schema::dropIfExists('invoice_details');
    }
}
