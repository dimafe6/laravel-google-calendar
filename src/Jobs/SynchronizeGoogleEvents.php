<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Carbon\Carbon;
use DateTimeInterface;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\Events;
use Google_Service_Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class SynchronizeGoogleEvents
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class SynchronizeGoogleEvents extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @inheritDoc
     */
    public function getGoogleRequest(Google_Service_Calendar $service, array $options): Events
    {
        // Default configuration. This is available only if syncToken is not provided
        if (!isset($options['syncToken'])) {
            $defaultOptions = [
                'maxResults' => config('googlecalendar.max_results_per_page'),
            ];

            if ($timeMin = config('googlecalendar.time_min_months')) {
                $defaultOptions['timeMin'] = now()->subMonths($timeMin)->format(DateTimeInterface::RFC3339);
            }

            if ($timeMax = config('googlecalendar.time_max_months')) {
                $defaultOptions['timeMax'] = now()->addMonths($timeMax)->format(DateTimeInterface::RFC3339);
            }

            $options = array_merge($defaultOptions, $options);
        }

        return $service->events->listEvents(
            $this->synchronizable->google_id, $options
        );
    }

    /**
     * @param array $googleEvents
     * @return void
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function syncItems(array $googleEvents): void
    {
        $googleEvents = collect($googleEvents);

        // Delete all local events that deleted on the google
        $deletedEventsIDs = $googleEvents->where('status', '=', 'cancelled')->pluck('id')->toArray();
        if (count($deletedEventsIDs)) {
            $this->synchronizable->events()
                ->whereIn('google_id', $deletedEventsIDs)
                ->delete();
        }

        $existsEvents = [];
        // Getting all changed google events
        $changedEvents = $googleEvents->whereNotIn('id', $deletedEventsIDs);
        if ($changedEvents->count()) {
            // Getting all events that exist in our database and not changed from the google
            $existsEvents = $this->synchronizable
                ->events()
                ->whereIn('google_id', $changedEvents->pluck('id')->toArray())
                ->pluck('google_id')
                ->toArray();
        }

        /** @var Event $event */
        foreach ($changedEvents as $event) {
            $data = [
                'google_id'          => $event->id,
                'google_calendar_id' => $this->synchronizable->id,
                'summary'            => $event->summary ?? '',
                'status'             => $event->status,
                'description'        => $event->description,
                'html_link'          => $event->htmlLink,
                'hangout_link'       => $event->hangoutLink,
                'organizer_email'    => optional($event->getOrganizer())->email,
                'date_start'         => $this->parseDatetime($event->start),
                'date_end'           => $this->parseDatetime($event->end),
                'all_day'            => $this->isAllDayEvent($event),
                'duration'           => $this->getDuration($event),
                'recurrence'         => $event->recurrence ? $event->recurrence[0] ?? null : null
            ];

            // If current event is exists in our DB then update this event
            if (count($existsEvents) && in_array($event->id, $existsEvents)) {
                $this->synchronizable->events()->where('google_id', $event->id)->update($data);
            } else {
                // If current event is don't exists in our DB then create this event
                $this->synchronizable->events()->create($data);
            }
        }
    }

    /**
     * Returns true if current event is "all day" event
     *
     * @param Event $googleEvent
     * @return bool
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    protected function isAllDayEvent(Event $googleEvent): bool
    {
        return !is_null($googleEvent->getStart()->date);
    }

    /**
     * Returns UTC time for provided event
     *
     * @param EventDateTime $googleDatetime
     * @return Carbon
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    protected function parseDatetime(EventDateTime $googleDatetime): Carbon
    {
        return Carbon::parse($googleDatetime->dateTime ?? $googleDatetime->date)->utc();
    }

    /**
     * Returns event duration in minutes
     *
     * @param Event $googleEvent
     * @return int
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    protected function getDuration(Event $googleEvent): int
    {
        return $this->parseDatetime($googleEvent->getEnd())
            ->diffInMinutes($this->parseDatetime($googleEvent->getStart()));
    }

    /**
     * @inheritDoc
     */
    public function dropAllSyncedItems(): void
    {
        $this->synchronizable->events()->delete();
    }
}
