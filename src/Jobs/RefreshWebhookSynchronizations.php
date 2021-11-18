<?php

namespace Dimafe6\GoogleCalendar\Jobs;

use Dimafe6\GoogleCalendar\Models\GoogleSynchronization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class RefreshWebhookSynchronizations
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar\Jobs
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class RefreshWebhookSynchronizations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Monitor webhook synchronizations and make sure they are refreshed before they expire
     *
     * @return void
     */
    public function handle()
    {
        GoogleSynchronization::query()
            ->whereNotNull('resource_id')
            ->whereNull('expired_at')
            ->orWhere('expired_at', '<', now()->addDays(2))
            ->get()
            ->each->refreshWebhook();
    }
}
