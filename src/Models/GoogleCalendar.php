<?php

namespace Dimafe6\GoogleCalendar\Models;

use Dimafe6\GoogleCalendar\Jobs\SynchronizeGoogleEvents;
use Dimafe6\GoogleCalendar\Models\Traits\Synchronizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class GoogleCalendar
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Models
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 * @property integer $id
 * @property string $google_id
 * @property integer $google_account_id
 * @property string $name
 * @property string $color
 * @property string $timezone
 */
class GoogleCalendar extends Model
{
    use Synchronizable;

    public const TABLE = 'google_calendars';

    protected $table = self::TABLE;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'google_id',
        'google_account_id',
        'name',
        'color',
        'timezone',
    ];

    public function synchronize(bool $force = false)
    {
        SynchronizeGoogleEvents::dispatch($this, $force);
    }

    /**
     * @return BelongsTo
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function googleAccount()
    {
        return $this->belongsTo(GoogleAccount::class);
    }

    /**
     * @return HasMany
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function events()
    {
        return $this->hasMany(GoogleCalendarEvent::class);
    }
}
