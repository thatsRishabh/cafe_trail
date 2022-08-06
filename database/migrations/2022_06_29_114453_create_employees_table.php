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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->bigInteger('mobile');
            $table->string('designation', 100);
            $table->string('email')->unique();
            $table->text('address');
            $table->date('joining_date')->comment('This will be in yyyy-mm-dd');
            $table->date('birth_date')->comment('This will be in yyyy-mm-dd');
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->integer('salary');
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
        Schema::dropIfExists('employees');
    }
};
