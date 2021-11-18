<?php

namespace Dimafe6\GoogleCalendar\Models;

use App\Models\User;
use Carbon\Carbon;
use Dimafe6\GoogleCalendar\Concerns\Synchronizable;
use Dimafe6\GoogleCalendar\Contracts\SynchronizableInterface;
use Dimafe6\GoogleCalendar\Facades\GoogleCalendar as CalendarFacade;
use Dimafe6\GoogleCalendar\Jobs\SynchronizeGoogleCalendars;
use Dimafe6\GoogleCalendar\Jobs\WatchGoogleCalendars;
use Dimafe6\GoogleCalendar\Models\GoogleCalendar as GoogleCalendarModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Class GoogleAccount
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Models
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 *
 * @property integer $id
 * @property string $google_id
 * @property integer $user_id
 * @property string $user_name
 * @property string $nickname
 * @property string $avatar
 * @property string $email
 * @property string $access_token
 * @property Carbon $access_token_expire
 * @property string $refresh_token
 * @property Collection $calendars
 * @property User $user
 */
class GoogleAccount extends Model implements SynchronizableInterface
{
    use Synchronizable;

    public const TABLE = 'google_accounts';

    protected $table = self::TABLE;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'google_id',
        'user_id',
        'user_name',
        'nickname',
        'avatar',
        'email',
        'access_token',
        'access_token_expire',
        'refresh_token',
    ];

    public $dates = ['access_token_expire'];

    /**
     * @inheritDoc
     */
    public function synchronize(bool $force = false): void
    {
        SynchronizeGoogleCalendars::dispatch($this, $force);
    }

    /**
     * @return BelongsTo
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function calendars(): HasMany
    {
        return $this->hasMany(GoogleCalendarModel::class);
    }

    /**
     * Returns google account access token. The access token will be refreshed if it is expired
     *
     * @return ?string
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getAccessTokenAttribute(): ?string
    {
        try {
            // Automatically update access token if it expired
            if ($this->access_token_expire && $this->access_token_expire->subMinutes(5)->isPast()) {
                if ($client = CalendarFacade::getGoogleClient($this->attributes['access_token'])) {
                    $result = $client->fetchAccessTokenWithRefreshToken($this->refresh_token);

                    if (isset($result['error'])) {
                        Log::error("User {$this->id} has invalid google access token");
                        Log::error($result['error_description']);

                        $this->delete();

                        return null;
                    }

                    $user = Socialite::driver('google')->userFromToken($client->getAccessToken()['access_token']);

                    self::update([
                        'access_token'        => $client->getAccessToken()['access_token'],
                        'refresh_token'       => $client->getAccessToken()['refresh_token'],
                        'access_token_expire' => now()->addSeconds($client->getAccessToken()['expires_in']),
                        'user_name'           => $user->name,
                        'email'               => $user->email,
                        'nickname'            => $user->nickname,
                        'avatar'              => $user->avatar,
                    ]);
                }
            }

            return $this->attributes['access_token'];
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @inheritDoc
     */
    public function watch()
    {
        WatchGoogleCalendars::dispatch($this);
    }
}
