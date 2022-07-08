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
        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->text('mobile')->nullable();
            $table->string('email')->unique();
            $table->text('address');
            $table->boolean('status')->default(1);
            $table->integer('pincode');
            // $table->renameColumn('address', 'myaddress');
            $table->timestamps();
        });
    }

    // $table->dropColumn('votes');
    // $table->renameColumn('from', 'to');
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_data');
    }
};
