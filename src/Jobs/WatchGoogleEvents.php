<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Google\Service\Calendar\Channel;
use Google_Service_Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class WatchGoogleEvents
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class WatchGoogleEvents extends WatchGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Returns request for watching a google events
     *
     * @param Google_Service_Calendar $service
     * @param Channel $channel
     * @return Channel
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleRequest(Google_Service_Calendar $service, Channel $channel): Channel
    {
        return $service->events->watch($this->synchronizable->google_id, $channel);
    }
}

