<?php

return [
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

    'max_results_per_page' => 2500,

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

    'time_min_months' => 6,

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

    'time_max_months' => 6,

    /*
    |--------------------------------------------------------------------------
    | Synchronization when google account is created
    |--------------------------------------------------------------------------
    |
    | If true, then synchronization calendars and events will be automatically run when a google account is created
    |
    */

    'sync_on_create' => false,
];
