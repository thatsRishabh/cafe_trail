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
            $table->string('product', 50);
            $table->string('category', 50);
            $table->string('subcategory', 50);
            $table->text('description');
            $table->integer('price') ->comment('This will be used to subtract from Expense');
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
