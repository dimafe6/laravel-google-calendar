<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GoogleCalendarEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('google_id');
            $table->integer('google_calendar_id');
            $table->text('summary');
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('html_link')->nullable();
            $table->string('hangout_link')->nullable();
            $table->string('organizer_email')->nullable();
            $table->timestamp('date_start');
            $table->timestamp('date_end');
            $table->integer('duration');
            $table->boolean('all_day')->default(false);
            $table->text('recurrence')->nullable();

            $table->foreign('google_calendar_id')
                ->references('id')->on('google_calendars')
                ->onDelete('cascade');

            $table->unique(['google_id', 'google_calendar_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_calendar_events');
    }
}
