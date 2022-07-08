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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('items', 100)->nullable();
            $table->integer('product_id')->nullable()->comment('Join here has to be made');
            $table->integer('quantity')->nullable();
            $table->integer('rate')->nullable();
            $table->integer('totalExpense')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('expenses');
    }
};
