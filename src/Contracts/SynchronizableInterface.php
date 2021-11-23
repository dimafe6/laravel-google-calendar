<?php

namespace Dimafe6\GoogleCalendar\Contracts;

use Dimafe6\GoogleCalendar\Models\GoogleAccount;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Interface SynchronizableInterface
 * @package Dimafe6\GoogleCalendar\Contracts
 * @author Dmytro Feshchenko <dimafe2000@gmail.com>
 * @property SynchronizationInterface $synchronization
 */
interface SynchronizableInterface
{
    /**
     * Boot logic for synchronizable model
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function bootSynchronizable();

    /**
     * @return MorphOne
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function synchronization(): MorphOne;

    /**
     * Implements logic for synchronization resource
     *
     * @param bool $force Clear all items and resync
     * @return void
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function synchronize(bool $force = false): void;

    /**
     * Returns google account access token
     *
     * @return ?string
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getAccessToken(): ?string;

    /**
     * Returns google account
     *
     * @return GoogleAccount|null
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleAccount(): ?GoogleAccount;

    /**
     * Start watching this resource
     *
     * @return void
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function watch();
}
