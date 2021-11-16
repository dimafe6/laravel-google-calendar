<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Carbon\Carbon;
use DateTimeInterface;
use Dimafe6\GoogleCalendar\Facades\GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\Events;
use Google_Service_Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SynchronizeGoogleEvents extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @return Google_Service_Calendar
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleService(): Google_Service_Calendar
    {
        return GoogleCalendar::getGoogleCalendarService($this->synchronizable->googleAccount->access_token);
    }

    /**
     * @param Google_Service_Calendar $service
     * @param $options
     * @return Events
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleRequest($service, $options): Events
    {
        if (!isset($options['syncToken'])) {
            $defaultOptions = [
                'maxResults' => config('googlecalendar.events.max_results_per_page'),
            ];

            if ($timeMin = config('googlecalendar.events.time_min_months')) {
                $defaultOptions['timeMin'] = now()->subMonths($timeMin)->format(DateTimeInterface::RFC3339);
            }

            if ($timeMax = config('googlecalendar.events.time_max_months')) {
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
    public function syncItems(array $googleEvents)
    {
        $googleEvents = collect($googleEvents);

        $deletedEventsIDs = $googleEvents->where('status', '=', 'cancelled')->pluck('id')->toArray();
        if (count($deletedEventsIDs)) {
            $this->synchronizable->events()
                ->whereIn('google_id', $deletedEventsIDs)
                ->delete();
        }

        $existsEvents = [];
        $changedEvents = $googleEvents->whereNotIn('id', $deletedEventsIDs);
        if ($changedEvents->count()) {
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
                'summary'            => $event->summary,
                'status'             => $event->status,
                'description'        => $event->description,
                'html_link'          => $event->htmlLink,
                'hangout_link'       => $event->hangoutLink,
                'organizer_email'    => optional($event->getOrganizer())->email,
                'date_start'         => $this->parseDatetime($event->start),
                'date_end'           => $this->parseDatetime($event->end),
                'all_day'            => $this->isAllDayEvent($event),
                'duration'           => $this->getDuration($event),
                'recurrence'         => $event->recurrence ? json_encode($event->recurrence) : null
            ];

            if (count($existsEvents) && in_array($event->id, $existsEvents)) {
                $this->synchronizable->events()->where('google_id', $event->id)->update($data);
            } else {
                $this->synchronizable->events()->create($data);
            }
        }
    }

    protected function isAllDayEvent($googleEvent)
    {
        return !is_null($googleEvent->getStart()->date);
    }

    protected function parseDatetime($googleDatetime)
    {
        return Carbon::parse(
            $googleDatetime->dateTime ?? $googleDatetime->date,
            $googleDatetime->timeZone
        );
    }

    protected function getDuration($googleEvent)
    {
        return $this->parseDatetime($googleEvent->end)->diffInMinutes($this->parseDatetime($googleEvent->start));
    }

    public function dropAllSyncedItems()
    {
        $this->synchronizable->events()->delete();
    }
}
