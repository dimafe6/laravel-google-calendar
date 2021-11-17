<?php

namespace Dimafe6\GoogleCalendar\Contracts;

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
}
