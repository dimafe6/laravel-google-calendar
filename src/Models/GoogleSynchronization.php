<?php

namespace Dimafe6\GoogleCalendar\Models;

use Carbon\Carbon;
use Dimafe6\GoogleCalendar\Contracts\SynchronizableInterface;
use Dimafe6\GoogleCalendar\Contracts\SynchronizationInterface;
use Dimafe6\GoogleCalendar\Facades\GoogleCalendar;
use Google_Service_Calendar;
use Google_Service_Calendar_Channel;
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
 * @property string $resource_id
 * @property Carbon $expired_at
 * @property SynchronizableInterface $synchronizable
 */
class GoogleSynchronization extends Model implements SynchronizationInterface
{
    public const TABLE = 'google_synchronizations';

    protected $table = self::TABLE;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'token',
        'last_synchronized_at',
        'resource_id',
        'expired_at'
    ];

    protected $dates = ['last_synchronized_at', 'expired_at'];

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

        static::creating(function (GoogleSynchronization $synchronization) {
            // Before creating a new synchronization,
            // ensure the UUID and the `last_synchronized_at` are set.
            $synchronization->id = Uuid::uuid4();
            $synchronization->last_synchronized_at = now();
        });

        static::created(function (GoogleSynchronization $synchronization) {
            $synchronization->startListeningForChanges();
            $synchronization->ping();
        });

        static::deleting(function ($synchronization) {
            // We stop the webhook (if applicable) right
            // before the synchronization is deleted.
            $synchronization->stopListeningForChanges();
        });
    }

    /**
     * @inheritDoc
     */
    public function asGoogleChannel(): Google_Service_Calendar_Channel
    {
        return tap(new Google_Service_Calendar_Channel(), function ($channel) {
            $channel->setId($this->id);
            $channel->setResourceId($this->resource_id);
            $channel->setType('web_hook');
            $channel->setAddress(route('google.calendar.webhook'));
        });
    }

    /**
     * @inheritDoc
     */
    public function startListeningForChanges(): void
    {
        $this->synchronizable->watch();
    }

    /**
     * @inheritDoc
     */
    public function stopListeningForChanges(): void
    {
        // If resource_id is null then the synchronization
        // does not have an associated Google Channel and
        // therefore there is nothing to stop at this point.
        if (!$this->resource_id) {
            return;
        }

        /** @var Google_Service_Calendar $service */
        $service = GoogleCalendar::getGoogleCalendarService($this->synchronizable->getAccessToken());

        if ($service) {
            $service->channels->stop($this->asGoogleChannel());
        }
    }

    /**
     * @inheritDoc
     */
    public function refreshWebhook(): void
    {
        $this->stopListeningForChanges();

        // Update the UUID since the previous one has
        // already been associated to a Google Channel.
        $this->update(['id' => Uuid::uuid4()]);

        $this->startListeningForChanges();
    }
}
