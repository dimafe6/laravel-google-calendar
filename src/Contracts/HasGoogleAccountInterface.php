<?php

namespace Dimafe6\GoogleCalendar\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Interface HasGoogleAccountInterface
 * @package Dimafe6\GoogleCalendar\Contracts
 * @author Dmytro Feshchenko <dimafe2000@gmail.com>
 */
interface HasGoogleAccountInterface
{
    /**
     * @return HasOne
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function googleAccount(): HasOne;
}
