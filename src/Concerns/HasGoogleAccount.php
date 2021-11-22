<?php

namespace Dimafe6\GoogleCalendar\Concerns;

use App\Models\User;
use Dimafe6\GoogleCalendar\Models\GoogleAccount;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Trait HasGoogleAccount
 * @package Dimafe6\GoogleCalendar\Concerns
 * @author Dmytro Feshchenko <dimafe2000@gmail.com>
 * @mixin User
 * @property GoogleAccount $googleAccount
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
