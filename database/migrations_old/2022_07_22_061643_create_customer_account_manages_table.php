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
        Schema::create('customer_account_manages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('This will be from customers(id) table')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 
            $table->integer('previous_balance')->nullable();
            $table->integer('change_in_balance')->nullable();
            $table->integer('new_balance')->nullable();
            $table->enum('transaction_type', ['Credit', 'Debit']);
            $table->string('mode_of_transaction')->comment('via cash or upi')->nullable();
            $table->boolean('account_status')->comment('1 means Active, 2 inactive')->nullable();  
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
        Schema::dropIfExists('customer_account_manages');
    }
};
