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
        Schema::table('recipe_contains', function (Blueprint $table) {
            $table->unsignedBigInteger('product_info_stock_id')->nullable()->after('id');
            $table->foreign('product_info_stock_id')->references('id')->on('product_infos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipe_contains', function (Blueprint $table) {
            //
        });
    }
};
