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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('table_number')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('order_status')->comment('0 means pending(by default), 1 means Approved')->default('0')->nullable();
            $table->integer('cartTotalQuantity')->nullable();
            $table->integer('cartTotalAmount')->nullable();
            $table->integer('taxes')->nullable();
            $table->integer('netAmount')->nullable(); 
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
        Schema::dropIfExists('orders');
    }
};
