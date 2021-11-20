<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |--------------------------------------------------------------------------
    |
    | Determines the path to the auth config JSON file from the google
    |
    */

    'google_auth_config_json' => env('GOOGLE_CALENDAR_AUTH_CONFIG', storage_path('app/google-calendar/client_secret.json')),

    /*
    |--------------------------------------------------------------------------
    | Maximum number of events per page
    |--------------------------------------------------------------------------
    |
    | Maximum number of events returned on one result page.
    | The number of events in the resulting page may be less than this value, or none at all,
    | even if there are more events matching the query.
    | By default the value is 250 events. The page size can never be larger than 2500 events.
    |
    */

    'max_results_per_page' => env('GOOGLE_CALENDAR_MAX_RESULTS_PER_PAGE', 2500),

    /*
    |--------------------------------------------------------------------------
    | Start period of synchronization
    |--------------------------------------------------------------------------
    |
    | Determines from how many months ago events should be synchronized.
    | For example, if 6 is specified, then events will be synchronized for the now()->subMonths(6)
    | Can be null for ignoring limitation
    |
    */

    'time_min_months' => env('GOOGLE_CALENDAR_TIME_MIN_MONTHS', 6),

    /*
    |--------------------------------------------------------------------------
    | End period of synchronization
    |--------------------------------------------------------------------------
    |
    | Determines how many months in advance the events need to be synchronized.
    | For example, if 6 is specified, then events will be synchronized for the now()->addMonths(6)
    | Can be null for ignoring limitation
    |
    */

    'time_max_months' => env('GOOGLE_CALENDAR_TIME_MAX_MONTHS', 6),

    /*
    |--------------------------------------------------------------------------
    | Synchronization when google account is created
    |--------------------------------------------------------------------------
    |
    | If true, then synchronization calendars and events will be automatically run when a google account is created
    |
    */

    'sync_on_create' => env('GOOGLE_CALENDAR_SYNC_ON_CREATE', false),

    /*
    |--------------------------------------------------------------------------
    | Google webhook URL
    |--------------------------------------------------------------------------
    |
    | The route path that listens to Google webhook notifications
    |
    */

    'webhook_uri' => env('GOOGLE_CALENDAR_WEBHOOK_URL', '/google/calendar/webhook'),

    /*
    |--------------------------------------------------------------------------
    | Refresh webhook job scheduling
    |--------------------------------------------------------------------------
    |
    | The cron expression to schedule job for refreshing webhook synchronizations.
    | Example: '0 1 * * *' will run job every day at 01:00
    |
    */

    'refresh_webhook_cron' => env('GOOGLE_CALENDAR_REFRESH_WEBHOOK_CRON', '0 1 * * *'),

    /*
    |--------------------------------------------------------------------------
    | Periodic synchronization job scheduling
    |--------------------------------------------------------------------------
    |
    | The cron expression to schedule a job for periodic sync all GoogleSynchronization models without resource_id.
    | That job will update all resources that cannot be updated through the webhook
    |
    */

    'periodic_sync_cron' => env('GOOGLE_CALENDAR_PERIODIC_SYNC_CRON', '*/15 * * * *'),

];
