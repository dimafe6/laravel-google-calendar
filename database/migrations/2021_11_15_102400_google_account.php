<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GoogleAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->unique();
            $table->integer('user_id');
            $table->string('user_name')->nullable();
            $table->string('nickname')->nullable();
            $table->text('avatar')->nullable();
            $table->string('email');
            $table->text('access_token');
            $table->timestamp('access_token_expire');
            $table->text('refresh_token');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_accounts');
    }
}
