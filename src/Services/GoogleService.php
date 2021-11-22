<?php

namespace Dimafe6\GoogleCalendar\Services;

use Google\Service\Calendar\Calendar;
use Google\Service\Calendar\CalendarListEntry;
use Google\Service\Calendar\Event;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_Event;
use Throwable;

/**
 * Class GoogleService
 *
 * @category PHP
 * @package  App\Services\Api
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class GoogleService
{
    /**
     * @param ?string $accessToken
     * @return ?Google_Client
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function getGoogleClient(?string $accessToken): ?Google_Client
    {
        if (!$accessToken) {
            return null;
        }

        $client = new Google_Client();
        $client->setApplicationName(config('googlecalendar.application_name'));
        $client->setAuthConfig(config('googlecalendar.google_auth_config_json'));
        $client->setAccessType('offline');
        $client->setAccessToken($accessToken);

        return $client;
    }

    /**
     * @param string|null $accessToken
     * @return Google_Service_Calendar|null
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function getGoogleCalendarService(?string $accessToken): ?Google_Service_Calendar
    {
        if (!$accessToken) {
            return null;
        }

        if ($client = self::getGoogleClient($accessToken)) {
            return new Google_Service_Calendar($client);
        }

        return null;
    }

    /**
     * @param string|null $accessToken
     * @param array $params
     * @return CalendarListEntry[]
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function getCalendars(?string $accessToken, array $params = []): array
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            return $calendarService->calendarList->listCalendarList($params)->getItems();
        }

        return [];
    }

    /**
     * @param ?string $accessToken
     * @param string $calendarId
     * @return ?Calendar
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function getCalendarById(?string $accessToken, string $calendarId): ?Calendar
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            return $calendarService->calendars->get($calendarId);
        }

        return null;
    }

    /**
     * @param ?string $accessToken
     * @param string $calendarId
     * @param string $eventId
     * @return Event|null
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function getEvent(?string $accessToken, string $calendarId, string $eventId): ?Event
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            return $calendarService->events->get($calendarId, $eventId);
        }

        return null;
    }

    /**
     * @param ?string $accessToken
     * @param string $summary
     * @return Calendar|null
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function createCalendarBySummary(?string $accessToken, string $summary): ?Calendar
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            $calendar = new Google_Service_Calendar_Calendar();
            $calendar->setSummary($summary);

            return $calendarService->calendars->insert($calendar);
        }

        return null;
    }

    /**
     * @param ?string $accessToken
     * @param string $calendarId
     * @param Google_Service_Calendar_Event $event
     * @return Event|null
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function createEvent(?string $accessToken, string $calendarId, Google_Service_Calendar_Event $event): ?Event
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            return $calendarService->events->insert($calendarId, $event);
        }

        return null;
    }

    /**
     * @param ?string $accessToken
     * @param string $calendarId
     * @param string $eventId
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function removeEvent(?string $accessToken, string $calendarId, string $eventId): void
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            $calendarService->events->delete($calendarId, $eventId);
        }
    }

    /**
     * @param ?string $accessToken
     * @param string $calendarId
     * @param string $eventId
     * @param Google_Service_Calendar_Event $event
     * @return Event|null
     * @throws Throwable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public static function updateEvent(?string $accessToken, string $calendarId, string $eventId, Google_Service_Calendar_Event $event): ?Event
    {
        if ($calendarService = self::getGoogleCalendarService($accessToken)) {
            return $calendarService->events->patch($calendarId, $eventId, $event);
        }

        return null;
    }
}
