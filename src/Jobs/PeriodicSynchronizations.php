<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Dimafe6\GoogleCalendar\Models\GoogleSynchronization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class PeriodicSynchronizations
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class PeriodicSynchronizations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * For some resources, webhook notifications are forbidden.
     * That GoogleSynchronization models will have an empty resource_id.
     * Thus, we need periodically run synchronization for that GoogleSynchronization models
     *
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function handle()
    {
        GoogleSynchronization::whereNull('resource_id')->get()->each->ping();
    }
}
