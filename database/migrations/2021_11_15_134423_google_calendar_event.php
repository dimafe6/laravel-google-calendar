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
            $table->text('google_id');
            $table->integer('google_calendar_id');
            $table->text('summary');
            $table->text('description')->nullable();
            $table->text('status')->nullable();
            $table->text('html_link')->nullable();
            $table->text('hangout_link')->nullable();
            $table->text('organizer_email')->nullable();
            $table->timestamp('date_start');
            $table->timestamp('date_end');
            $table->integer('duration');
            $table->boolean('all_day')->default(false);
            $table->json('recurrence')->nullable();
            $table->text('recurring_event_id')->nullable();

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
