<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Dimafe6\GoogleCalendar\Contracts\SynchronizableInterface;
use Dimafe6\GoogleCalendar\Contracts\SynchronizationInterface;
use Dimafe6\GoogleCalendar\Facades\GoogleCalendar;
use Google\Service\Calendar\Channel;
use Google_Service_Calendar;
use Google_Service_Exception;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Carbon;

/**
 * Class WatchGoogleResource
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
abstract class WatchGoogleResource
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 5;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    protected SynchronizableInterface $synchronizable;
    protected SynchronizationInterface $synchronization;

    /**
     * Constructor for WatchGoogleResource
     *
     * @param SynchronizableInterface $synchronizable
     */
    public function __construct(SynchronizableInterface $synchronizable)
    {
        $this->synchronizable = $synchronizable;
        $this->synchronization = $synchronizable->synchronization;
    }

    /**
     * Implements logic for start watching google resource
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function handle()
    {
        $service = GoogleCalendar::getGoogleCalendarService($this->synchronizable->getAccessToken());

        if ($service) {
            try {
                $response = $this->getGoogleRequest($service, $this->synchronization->asGoogleChannel());

                // We can now update our synchronization model
                // with the provided resource_id and expired_at.
                $this->synchronization->update([
                    'resource_id' => $response->getResourceId(),
                    'expired_at'  => Carbon::createFromTimestampMs($response->getExpiration())
                ]);
            } catch (Google_Service_Exception $e) {
                if ($e->getCode() === 401) {
                    optional($this->synchronizable->getGoogleAccount())->forceLogout();
                }

                // If we reach an error at this point, it is likely that
                // webhook notifications are forbidden for this resource.
                // Instead, we will sync it manually at regular interval.
            }
        }
    }

    /**
     * Prevent overlapping
     *
     * @return array
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function middleware()
    {
        return [(new WithoutOverlapping($this->synchronization->id))];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return range(30, 300, 270 / $this->tries);
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

