<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Dimafe6\GoogleCalendar\Facades\GoogleCalendar;
use Dimafe6\GoogleCalendar\Models\GoogleAccount;
use Google\Service\Calendar\CalendarList;
use Google\Service\Calendar\CalendarList as CalendarListModel;
use Google\Service\Calendar\CalendarListEntry;
use Google_Service_Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class SynchronizeGoogleCalendars extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @return Google_Service_Calendar
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleService(): Google_Service_Calendar
    {
        return GoogleCalendar::getGoogleCalendarService($this->synchronizable->access_token);
    }

    /**
     * @param Google_Service_Calendar $service
     * @param $options
     * @return CalendarListModel
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function getGoogleRequest($service, $options): CalendarListModel
    {
        return $service->calendarList->listCalendarList($options);
    }

    /**
     * @param CalendarListEntry[] $googleCalendars
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function syncItems(array $googleCalendars)
    {
        $calendars = collect($googleCalendars);

        $deletedCalendarsIDs = $calendars->where('deleted', '=', true)->pluck('id')->toArray();
        if (count($deletedCalendarsIDs)) {
            $this->synchronizable->calendars()
                ->whereIn('google_id', $deletedCalendarsIDs)
                ->delete();
        }

        $newCalendars = $calendars->whereNotIn('id', $deletedCalendarsIDs);

        try {
            DB::beginTransaction();

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

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function dropAllSyncedItems()
    {
        $this->synchronizable->calendars()->delete();
    }
}
