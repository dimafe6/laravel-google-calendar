<?php

namespace Dimafe6\GoogleCalendar\Models;

use Carbon\Carbon;
use Dimafe6\GoogleCalendar\Contracts\SynchronizationInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ramsey\Uuid\Uuid;

/**
 * Class GoogleSynchronization
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Models
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 * @property integer $id
 * @property string $token
 * @property Carbon $last_synchronized_at
 */
class GoogleSynchronization extends Model implements SynchronizationInterface
{
    public const TABLE = 'google_synchronizations';

    protected $table = self::TABLE;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'token',
        'last_synchronized_at'
    ];

    protected $dates = ['last_synchronized_at'];

    /**
     * @inheritDoc
     */
    public function ping(): void
    {
        $this->synchronizable->synchronize();
    }

    /**
     * @inheritDoc
     */
    public function synchronizable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();

        // Before creating a new synchronization,
        // ensure the UUID and the `last_synchronized_at` are set.
        static::creating(function (GoogleSynchronization $synchronization) {
            $synchronization->id = Uuid::uuid4();
            $synchronization->last_synchronized_at = now();
        });

        // Initial ping.
        static::created(function ($synchronization) {
            $synchronization->ping();
        });
    }

}
