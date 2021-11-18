<?php

namespace Dimafe6\GoogleCalendar\Http\Controllers;

use App\Http\Controllers\Controller;
use Dimafe6\GoogleCalendar\Models\GoogleSynchronization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleWebhookController extends Controller
{
    /**
     * Webhooks can have two states `exists` or `sync`.
     * `sync` webhooks are just notifications telling us that a
     * new webhook has been created. Since we already performed
     * an initial synchronization we can safely ignore them.
     *
     * @param Request $request
     * @return JsonResponse
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function __invoke(Request $request)
    {
        if ($request->header('x-goog-resource-state') !== 'exists') {
            return new JsonResponse([]);
        }

        $resourceId = $request->header('x-goog-resource-id');
        $channelId = $request->header('x-goog-channel-id');

        if (!$resourceId || !$channelId) {
            Log::error('Google Calendar Webhook: Requested resource id or channel id is empty. Maybe this request was not from Google?');

            return new JsonResponse([]);
        }

        /** @var GoogleSynchronization $synchronization */
        $synchronization = GoogleSynchronization::query()
            ->where('id', $channelId)
            ->where('resource_id', $resourceId)
            ->first();

        if (!$synchronization) {
            Log::warning(sprintf(
                'Google Calendar Webhook: Requested synchronization for channel id "%s" and resource id "%s" is not found!',
                $channelId,
                $resourceId
            ));

            return new JsonResponse([]);
        }

        Log::info(sprintf('Google Calendar Webhook: Run synchronization resource "%s".', $resourceId));
        $synchronization->ping();

        return new JsonResponse([]);
    }
}
