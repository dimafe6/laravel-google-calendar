<?php

namespace Dimafe6\GoogleCalendar\Contracts;

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
}
