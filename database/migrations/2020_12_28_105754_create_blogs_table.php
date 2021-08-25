<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->text('blog_name', 191)->nullable()->index();
            $table->text('blog_image', 191)->nullable(); 
			$table->integer('category_id')->default(0)->index();
            $table->string('banner')->nullable();
            $table->string('blog_description')->nullable();
            $table->dateTime('blog_date')->nullable();
            $table->text('seo_title', 255)->nullable();
            $table->text('seo_description', 255)->nullable();
            $table->text('seo_keyword', 255)->nullable();
            $table->integer('display_order')->nullable();
            $table->string('slug', 191)->nullable()->index(); 
            $table->integer('viewed')->nullable(); 
            $table->integer('created_by')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('blogs');
    }
}
