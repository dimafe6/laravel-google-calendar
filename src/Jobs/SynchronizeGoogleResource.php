<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Google_Service_Exception;

abstract class SynchronizeGoogleResource
{
    protected $synchronizable;
    protected $synchronization;
    protected bool $force = false;

    public function __construct($synchronizable, bool $force = false)
    {
        $this->synchronizable = $synchronizable;
        $this->synchronization = $synchronizable->synchronization;
        $this->force = $force;
    }

    /**
     * @throws Google_Service_Exception
     */
    public function handle()
    {
        // Start with an empty page token.
        $pageToken = null;
        $syncToken = $this->force ? null : $this->synchronization->token;

        // Delegate service instantiation to the subclass.
        $service = $this->getGoogleService();

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

    abstract public function getGoogleService();

    abstract public function getGoogleRequest($service, $options);

    abstract public function syncItems(array $items);

    abstract public function dropAllSyncedItems();
}

