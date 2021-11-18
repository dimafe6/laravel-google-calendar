<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleSynchronizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_synchronizations', function (Blueprint $table) {
            $table->string('id');
            $table->morphs('synchronizable'); // Reference to the model which will be synchronized
            $table->string('token')->nullable()->comment('syncToken provided by the google calendar API');
            $table->datetime('last_synchronized_at')->comment('Date of the last synchronization');
            $table->string('resource_id')->nullable()->comment('Google channel resource ID');
            $table->datetime('expired_at')->nullable()->comment('Google channel resource expired date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_synchronizations');
    }
}
