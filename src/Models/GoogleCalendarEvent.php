<?php

namespace Dimafe6\GoogleCalendar\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class GoogleCalendarEvent
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Models
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 *
 * @property integer $id
 * @property string $google_id
 * @property integer $google_calendar_id
 * @property string $summary
 * @property string $description
 * @property string $status
 * @property string $html_link
 * @property string $hangout_link
 * @property string $organizer_email
 * @property Carbon $date_start
 * @property Carbon $date_end
 * @property integer $duration
 * @property boolean $all_day
 * @property string $recurrence
 * @property GoogleCalendar $calendar
 */
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

    protected $dates = ['date_start', 'date_end'];

    /**
     * @return BelongsTo
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function calendar()
    {
        return $this->belongsTo(GoogleCalendar::class, 'google_calendar_id');
    }
}
