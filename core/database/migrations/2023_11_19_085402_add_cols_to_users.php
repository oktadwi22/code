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
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('zip_code', 40)->nullable();
            $table->string('cover_img')->nullable();
            $table->text('profile_heading')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('show_country')->default(false);
            $table->json('email_settings')->nullable();
            $table->json('social_media_settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
