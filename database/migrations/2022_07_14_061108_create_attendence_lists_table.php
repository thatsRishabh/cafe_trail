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
        Schema::create('attendence_lists', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('This will be in yyyy-mm-dd')->nullable();
            $table->unsignedBigInteger('attendence_id')->nullable();
            $table->foreign('attendence_id')->references('id')->on('employee_attendences')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->boolean('attendence')->comment('1 means absent, 2 means present')->nullable();
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
        Schema::dropIfExists('attendence_lists');
    }
};
