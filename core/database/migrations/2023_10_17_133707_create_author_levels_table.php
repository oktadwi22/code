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
        Schema::create('author_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('minimum_earning', 28, 8)->default(0);
            $table->decimal('commission', 28, 8)->default(0);
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
        Schema::dropIfExists('author_levels');
    }
};
