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
        Schema::table('product_stock_manages', function (Blueprint $table) {
            // $table->unsignedBigInteger('product_id')->comment('This will be from product_infos(id) table')->nullable()->change();
            // $table->foreign('product_id')->references('id')->on('product_infos')->onDelete('cascade'); 
            $table->renameColumn('name_id', 'product_id');
            $table->unsignedBigInteger('unit_id')->comment('This will be from unit(id) table')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_stock_manages', function (Blueprint $table) {
            //
        });
    }
};
