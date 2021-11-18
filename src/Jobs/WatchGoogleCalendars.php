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
 * Class WatchGoogleCalendars
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class WatchGoogleCalendars extends WatchGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Returns request for watching google calendars
     *
     * @param Google_Service_Calendar $service
     * @param Channel $channel
     * @return Channel
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleRequest(Google_Service_Calendar $service, Channel $channel): Channel
    {
        return $service->calendarList->watch($channel);
    }
}

