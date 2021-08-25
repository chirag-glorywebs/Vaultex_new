<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('profilepic')->nullable();
            $table->string('salesperson')->nullable();
            $table->string('enterprise_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('business_exp')->nullable();
            $table->string('sales')->nullable();
            $table->string('turn_over')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('downloadable')->nullable();
            $table->string('payment_interval')->nullable();
            $table->string('business_logo')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->integer('user_role')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('profilepic');
            $table->dropColumn('salesperson');
            $table->dropColumn('enterprise_name');
            $table->dropColumn('industry');
            $table->dropColumn('business_exp');
            $table->dropColumn('sales');
            $table->dropColumn('turn_over');
            $table->dropColumn('payment_mode');
            $table->dropColumn('downloadable');
            $table->dropColumn('payment_interval');
            $table->dropColumn('business_logo');
            $table->dropColumn('status');
            $table->dropColumn('user_role');
        });
    }
}
