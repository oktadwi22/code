<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('title');
            $table->unsignedBigInteger('category_id');
            $table->string('category')->nullable();
            $table->string('image');
            $table->string('license');
            $table->boolean('is_extended');
            $table->decimal('extended_amount', 5, 2);
            $table->decimal('price', 5, 2);
            $table->decimal('seller_fee', 5, 2);
            $table->decimal('buyer_fee', 5, 2);
            $table->integer('quantity');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
