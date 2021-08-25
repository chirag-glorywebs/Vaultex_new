<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->text('category_name', 191)->nullable();
            $table->text('category_image', 191)->nullable();
			$table->text('category_icon', 255)->nullable();
			$table->integer('parent_id')->default(0)->index();
            $table->string('banner')->nullable();
            $table->string('category_description')->nullable();
            $table->text('seo_title', 255)->nullable();
            $table->text('seo_description', 255)->nullable();
            $table->text('seo_keyword', 255)->nullable();
            $table->integer('display_order')->nullable();
            $table->string('slug', 191)->nullable()->index(); 
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
        Schema::dropIfExists('blog_categories');
    }
}
