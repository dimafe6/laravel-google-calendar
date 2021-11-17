<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleCalendar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->unique()->comment('Google calendar identifier');
            $table->integer('google_account_id')->comment('Reference to the google_accounts table');
            $table->text('name')->comment('Calendar name');
            $table->string('color')->nullable()->comment('Calendar color');
            $table->string('timezone')->comment('Calendar timezone');

            $table->foreign('google_account_id')
                ->references('id')->on('google_accounts')
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
        Schema::dropIfExists('google_calendars');
    }
}
