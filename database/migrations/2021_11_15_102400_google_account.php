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
            $table->string('google_id')->comment('Google account identifier');
            $table->integer('user_id')->comment('Reference to the users table');
            $table->string('user_name')->nullable()->comment('Google account user name');
            $table->string('nickname')->nullable()->comment('Google account user nickname');
            $table->text('avatar')->nullable()->comment('Google account user avatar');
            $table->string('email')->comment('Google account user email');
            $table->text('access_token')->comment('Access token for this google account');
            $table->timestamp('access_token_expire')->comment('Access token expire date');
            $table->text('refresh_token')->comment('Refresh token. Required for updating access token when that is expired');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->unique(['google_id', 'user_id']);
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
