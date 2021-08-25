<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id()->comment = 'Unique Id of the settings';
            $table->string('name', 200)->nullable()->comment = 'Name of the settings';
            $table->text('value')->nullable()->comment = 'Value of the settings';
            $table->timestamp('settings_updated_at')->useCurrent()->comment('Timestamp when admin updated the settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
