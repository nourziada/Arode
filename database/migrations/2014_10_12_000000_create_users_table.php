<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('mobile')->nullable();
            $table->string('social_media_token')->nullable();
            $table->string('token')->nullable();
            $table->string('image')->default('user.png');
            $table->string('status')->default(1);
            $table->string('firebase_token')->nullable();
            $table->string('rating')->default(0);
            $table->string('wallet')->default(0);
            $table->string('auth_code')->nullable();
            $table->string('is_auth')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
