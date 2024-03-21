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
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->text('tags')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('file')->nullable();
            $table->string('demo_url')->nullable();
            $table->text('attribute_info')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending,1=accepted,3=rejected');
            $table->bigInteger('accepted_by')->nullable();
            $table->string('version', 40)->nullable();
            $table->decimal('price', 28, 8)->default(0)->comment('Price for personal licnese');
            $table->decimal('price_cl', 28, 8)->default(0)->comment('Price for commercial licnese');
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
        Schema::dropIfExists('change_logs');
    }
};
