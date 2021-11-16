<?php

namespace Dimafe6\GoogleCalendar\Models\Traits;

use Dimafe6\GoogleCalendar\Models\GoogleSynchronization;

trait Synchronizable
{
    /**
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function bootSynchronizable()
    {
        static::created(function ($synchronizable) {
            $synchronizable->synchronization()->create();
        });

        static::deleting(function ($synchronizable) {
            optional($synchronizable->synchronization)->delete();
        });
    }

    /**
     * @return mixed
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function synchronization()
    {
        return $this->morphOne(GoogleSynchronization::class, 'synchronizable');
    }

    /**
     * @return mixed
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    abstract public function synchronize(bool $force = false);
}
