<?php

namespace Dimafe6\GoogleCalendar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class GoogleSynchronization extends Model
{
    public const TABLE = 'google_synchronizations';

    protected $table = self::TABLE;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'token',
        'last_synchronized_at'
    ];

    protected $casts = [
        'last_synchronized_at' => 'datetime',
    ];

    // Ask the synchronizable to dispatch the relevant job.
    public function ping()
    {
        return $this->synchronizable->synchronize();
    }

    // Create a polymorphic relationship to Google accounts and Calendars.
    public function synchronizable()
    {
        return $this->morphTo();
    }

    // Add global model listeners
    public static function boot()
    {
        parent::boot();

        // Before creating a new synchronization,
        // ensure the UUID and the `last_synchronized_at` are set.
        static::creating(function ($synchronization) {
            $synchronization->id = Uuid::uuid4();
            $synchronization->last_synchronized_at = now();
        });

        // Initial ping.
        static::created(function ($synchronization) {
            $synchronization->ping();
        });
    }

}
