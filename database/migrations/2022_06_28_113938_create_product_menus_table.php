<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('category_id', 50)->nullable();
            $table->string('subcategory_id', 50)->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_parent')->nullable();
            $table->string('image')->nullable();
            $table->integer('order_duration')->nullable();
            $table->text('image_url')->nullable();
            $table->integer('price') ->comment('This will be used to subtract from Expense')->nullable();
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
        Schema::dropIfExists('product_menus');
    }
};
