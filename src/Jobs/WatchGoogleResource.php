<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Dimafe6\GoogleCalendar\Contracts\SynchronizableInterface;
use Dimafe6\GoogleCalendar\Facades\GoogleCalendar;
use Google\Service\Calendar\Channel;
use Google_Service_Calendar;
use Google_Service_Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class WatchGoogleResource
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
abstract class WatchGoogleResource
{
    protected SynchronizableInterface $synchronizable;

    /**
     * Constructor for WatchGoogleResource
     *
     * @param SynchronizableInterface $synchronizable
     */
    public function __construct(SynchronizableInterface $synchronizable)
    {
        $this->synchronizable = $synchronizable;
    }

    /**
     * Implements logic for start watching google resource
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function handle()
    {
        $synchronization = $this->synchronizable->synchronization;

        $service = GoogleCalendar::getGoogleCalendarService($this->synchronizable->getAccessToken());

        if ($service) {
            try {
                $response = $this->getGoogleRequest($service, $synchronization->asGoogleChannel());
                Log::info('WatchGoogleResource');

                // We can now update our synchronization model
                // with the provided resource_id and expired_at.
                $synchronization->update([
                    'resource_id' => $response->getResourceId(),
                    'expired_at'  => Carbon::createFromTimestampMs($response->getExpiration())
                ]);
            } catch (Google_Service_Exception $e) {
                Log::warning($e->getMessage());

                // If we reach an error at this point, it is likely that
                // webhook notifications are forbidden for this resource.
                // Instead, we will sync it manually at regular interval.
            }
        }
    }

    /**
     * Request for creating google calendar channel
     *
     * @param Google_Service_Calendar $service
     * @param Channel $channel
     * @return Channel
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    abstract public function getGoogleRequest(Google_Service_Calendar $service, Channel $channel): Channel;
}

