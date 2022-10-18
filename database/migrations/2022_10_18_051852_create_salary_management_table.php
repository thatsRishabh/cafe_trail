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
        Schema::create('salary_management', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('This will be from employee(id) table')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->integer('previous_balance')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('new_balance')->nullable();
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
        Schema::dropIfExists('salary_management');
    }
};
