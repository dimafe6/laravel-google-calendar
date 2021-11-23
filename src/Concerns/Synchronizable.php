<?php

namespace Dimafe6\GoogleCalendar\Concerns;

use Dimafe6\GoogleCalendar\Contracts\SynchronizableInterface;
use Dimafe6\GoogleCalendar\Models\GoogleAccount;
use Dimafe6\GoogleCalendar\Models\GoogleSynchronization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait Synchronizable
 * @package Dimafe6\GoogleCalendar\Concerns
 * @author Dmytro Feshchenko <dimafe2000@gmail.com>
 * @mixin Model
 * @implements SynchronizableInterface
 */
trait Synchronizable
{
    /**
     * Boot logic for synchronizable model
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function bootSynchronizable()
    {
        static::created(function ($synchronizable) {
            if (config('googlecalendar.sync_on_create')) {
                $synchronizable->synchronization()->create();
            }
        });

        static::deleting(function ($synchronizable) {
            optional($synchronizable->synchronization)->delete();
        });
    }

    /**
     * @return MorphOne
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function synchronization(): MorphOne
    {
        return $this->morphOne(GoogleSynchronization::class, 'synchronizable');
    }

    /**
     * Implements logic for synchronization resource
     *
     * @param bool $force Clear all items and resync
     * @return void
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    abstract public function synchronize(bool $force = false): void;

    /**
     * @inheritDoc
     */
    abstract public function getAccessToken(): ?string;

    /**
     * @inheritDoc
     */
    abstract public function getGoogleAccount(): ?GoogleAccount;
}
