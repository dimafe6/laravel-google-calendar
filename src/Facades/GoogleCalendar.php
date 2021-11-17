<?php

namespace Dimafe6\GoogleCalendar\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class GoogleCalendar
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Facades
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
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
