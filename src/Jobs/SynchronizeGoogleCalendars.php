<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Google\Service\Calendar\CalendarList;
use Google\Service\Calendar\CalendarList as CalendarListModel;
use Google\Service\Calendar\CalendarListEntry;
use Google_Service_Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class SynchronizeGoogleCalendars
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class SynchronizeGoogleCalendars extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @inheritDoc
     */
    public function getGoogleRequest(Google_Service_Calendar $service, array $options): CalendarListModel
    {
        return $service->calendarList->listCalendarList($options);
    }

    /**
     * @param CalendarListEntry[] $googleCalendars
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function syncItems(array $googleCalendars): void
    {
        $calendars = collect($googleCalendars);

        // Delete all local calendars that deleted on the google
        $deletedCalendarsIDs = $calendars->where('deleted', '=', true)->pluck('id')->toArray();
        if (count($deletedCalendarsIDs)) {
            $this->synchronizable->calendars()
                ->whereIn('google_id', $deletedCalendarsIDs)
                ->delete();
        }

        // Get all not deleted google calendars
        $newCalendars = $calendars->whereNotIn('id', $deletedCalendarsIDs)->unique('id');

        // Create or update all google calendars on our database
        foreach ($newCalendars as $googleCalendar) {
            $this->synchronizable->calendars()->updateOrCreate(
                [
                    'google_id' => $googleCalendar->id,
                ],
                [
                    'name'     => $googleCalendar->summary,
                    'color'    => $googleCalendar->backgroundColor,
                    'timezone' => $googleCalendar->timeZone,
                ]
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function dropAllSyncedItems(): void
    {
        $this->synchronizable->calendars()->delete();
    }
}
