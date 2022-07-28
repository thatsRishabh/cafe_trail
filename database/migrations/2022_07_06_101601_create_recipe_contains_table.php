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
        Schema::create('recipe_contains', function (Blueprint $table) {
            $table->id();
            $table->integer('product_info_stock_id', 20);
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->string('name', 255)->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('unit_name', 50)->nullable();
            $table->integer('unit_minValue')->nullable();
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
        Schema::dropIfExists('recipe_contains');
    }
};
