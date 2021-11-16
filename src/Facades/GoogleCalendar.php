<?php

namespace Dimafe6\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleCalendar extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'googlecalendar';
    }
}
