<?php

namespace Dimafe6\GoogleCalendar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCalendarEvent extends Model
{
    public const TABLE = 'google_calendar_events';

    protected $table = self::TABLE;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'google_id',
        'google_calendar_id',
        'summary',
        'description',
        'status',
        'html_link',
        'hangout_link',
        'organizer_email',
        'date_start',
        'date_end',
        'duration',
        'all_day',
        'recurrence',
    ];

    /**
     * @return BelongsTo
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function calendar()
    {
        return $this->belongsTo(GoogleCalendar::class);
    }
}
