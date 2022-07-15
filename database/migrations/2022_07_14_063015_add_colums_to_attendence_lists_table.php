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
        Schema::table('attendence_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('attendence_id')->nullable();
            $table->foreign('attendence_id')->references('id')->on('employee_attendences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendence_lists', function (Blueprint $table) {
            //
        });
    }
};
