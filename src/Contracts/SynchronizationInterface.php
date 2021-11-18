<?php

namespace Dimafe6\GoogleCalendar\Contracts;

use Google_Service_Calendar_Channel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface SynchronizationInterface
 * @package Dimafe6\GoogleCalendar\Contracts
 * @author Dmytro Feshchenko <dimafe2000@gmail.com>
 */
interface SynchronizationInterface
{
    /**
     * Ask the synchronizable to dispatch the relevant job.
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function ping(): void;

    /**
     * Create a polymorphic relationship to Google accounts and Calendars.
     *
     * @return MorphTo
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function synchronizable(): MorphTo;

    /**
     * Transform synchronization model to google Channel
     *
     * @return Google_Service_Calendar_Channel
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function asGoogleChannel(): Google_Service_Calendar_Channel;

    /**
     * Runs a job for starting watching a synchronizable resource
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function startListeningForChanges(): void;

    /**
     * Stop watching synchronizable resource
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function stopListeningForChanges(): void;

    /**
     * Stop and then start watching a synchronizable resource. Channel will be recreated with a new UUID
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function refreshWebhook(): void;
}
