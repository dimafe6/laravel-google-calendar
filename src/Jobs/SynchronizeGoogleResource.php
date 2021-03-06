<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Dimafe6\GoogleCalendar\Contracts\SynchronizableInterface;
use Dimafe6\GoogleCalendar\Contracts\SynchronizationInterface;
use Dimafe6\GoogleCalendar\Facades\GoogleCalendar;
use Google_Service_Calendar;
use Google_Service_Exception;
use Illuminate\Queue\Middleware\WithoutOverlapping;

/**
 * Class SynchronizeGoogleResource
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
abstract class SynchronizeGoogleResource
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
    protected bool $force = false;

    /**
     * @param SynchronizableInterface $synchronizable
     * @param bool $force Clear all items and resync
     */
    public function __construct(SynchronizableInterface $synchronizable, bool $force = false)
    {
        $this->synchronizable = $synchronizable;
        $this->synchronization = $synchronizable->synchronization;
        $this->force = $force;
    }

    /**
     * Implements logic for getting resources with pagination
     *
     * @throws Google_Service_Exception
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function handle(): bool
    {
        // Start with an empty page token.
        $pageToken = null;
        $syncToken = $this->force ? null : $this->synchronization->token;

        $service = GoogleCalendar::getGoogleCalendarService($this->synchronizable->getAccessToken());

        if ($service) {
            do {
                try {
                    $options = compact('pageToken');
                    if ($syncToken) {
                        $options['syncToken'] = $syncToken;
                    }

                    $list = $this->getGoogleRequest($service, $options);
                    // If we catch a Google_Service_Exception with a 410 status code.
                } catch (Google_Service_Exception $e) {
                    if ($e->getCode() === 410) {
                        // Remove the synchronization's token.
                        $this->synchronization->update(['token' => null]);

                        // Drop all items (delegate this task to the subclasses).
                        $this->dropAllSyncedItems();

                        // Start again.
                        return $this->handle();
                    }

                    if ($e->getCode() === 401) {
                        optional($this->synchronizable->getGoogleAccount())->forceLogout();

                        return true;
                    }

                    throw $e;
                }

                $this->syncItems($list->getItems());

                // Get the new page token from the response.
                $pageToken = $list->getNextPageToken();

                // Continue until the new page token is null.
            } while ($pageToken);

            $this->synchronization->update([
                'token'                => $list->getNextSyncToken(),
                'last_synchronized_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Prevent overlapping
     *
     * @return array
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function middleware()
    {
        return [(new WithoutOverlapping($this->synchronization->synchronizable_id))];
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
     * Returns a google API request for getting items for synchronization
     *
     * @param Google_Service_Calendar $service
     * @param array $options
     * @return mixed
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    abstract public function getGoogleRequest(Google_Service_Calendar $service, array $options);

    /**
     * Logic for syncing items
     *
     * @param array $items
     * @return void
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    abstract public function syncItems(array $items): void;

    /**
     * Logic for clear all synced items
     *
     * @return void
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    abstract public function dropAllSyncedItems(): void;
}

