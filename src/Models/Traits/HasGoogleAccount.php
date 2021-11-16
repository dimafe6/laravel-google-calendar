<?php

namespace Dimafe6\GoogleCalendar\Models\Traits;

use App\Models\User;
use Dimafe6\GoogleCalendar\Models\GoogleAccount;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Trait HasGoogleAccount
 * @package Dimafe6\GoogleCalendar\Models\Traits
 * @author Dmytro Feshchenko <dimafe2000@gmail.com>
 * @mixin User
 */
trait HasGoogleAccount
{
    /**
     * @return HasOne
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function googleAccount(): HasOne
    {
        return $this->hasOne(GoogleAccount::class);
    }
}
