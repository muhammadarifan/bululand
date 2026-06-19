<?php

namespace App\Services;

use App\DTO\GowaWebhookPayload;
use App\Jobs\AutoReplyGowaWebhookJob;
use App\Models\GowaWebhookEvent;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

final class ProcessGowaWebhookService
{
    public function handle(array $payload): void
    {
        $normalized = $this->normalizePayload($payload);
        $webhookPayload = GowaWebhookPayload::from($normalized);

        Log::info('Gowa webhook payload received 2', [
            'payload' => $webhookPayload->all(),
        ]);

        $eventId = $webhookPayload->messageId();

        if ($eventId === null) {
            return;
        }

        $this->process($webhookPayload);

        try {
            $event = GowaWebhookEvent::query()->create([
                'event_id' => $eventId,
                'payload' => $webhookPayload->all(),
                'processed_at' => now(),
            ]);
        } catch (QueryException $exception) {
            if ($this->isDuplicateEvent($exception)) {
                return;
            }

            throw $exception;
        }
    }

    /**
     * Normalize nested webhook payload structure.
     *
     * GoWA webhook mengirim payload dengan struktur:
     * {
     *   "reply": null,
     *   "payload": {
     *     "device_id": "...",
     *     "event": "message",
     *     "payload": {
     *       "body": "...",
     *       "chat_id": "...",
     *       "from": "...",
     *       "id": "...",
     *       "timestamp": "..."
     *     }
     *   }
     * }
     *
     * Kita perlu menormalisasi menjadi struktur flat yang dimengerti GowaWebhookPayload:
     * {
     *   "event": "message",
     *   "device": "...",
     *   "from": "...",
     *   "chat_id": "...",
     *   "message": { "body": "...", "id": "...", "timestamp": "..." }
     * }
     */
    private function normalizePayload(array $payload): array
    {
        // Check if payload has the nested structure (payload.payload)
        $innerPayload = data_get($payload, 'payload.payload');
        $outerPayload = data_get($payload, 'payload');

        // If no nested structure, return as-is
        if (! is_array($innerPayload) || ! is_array($outerPayload)) {
            return $payload;
        }

        return [
            'event' => data_get($outerPayload, 'event', 'message'),
            'device' => data_get($outerPayload, 'device_id'),
            'from' => data_get($innerPayload, 'from'),
            'from_name' => data_get($innerPayload, 'from_name'),
            'from_lid' => data_get($innerPayload, 'from_lid'),
            'chat_id' => data_get($innerPayload, 'chat_id'),
            'chat_lid' => data_get($innerPayload, 'chat_lid'),
            'is_from_me' => data_get($innerPayload, 'is_from_me'),
            'message' => $innerPayload,
        ];
    }

    private function process(GowaWebhookPayload $payload): void
    {
        Log::info('Gowa webhook payload processed', [
            'payload' => $payload->all(),
        ]);
        AutoReplyGowaWebhookJob::dispatch($payload->all());
    }

    private function isDuplicateEvent(QueryException $exception): bool
    {
        return in_array($exception->getCode(), [23000, '23000'], true)
            || str_contains($exception->getMessage(), 'Duplicate entry')
            || str_contains($exception->getMessage(), 'duplicate key value')
            || str_contains($exception->getMessage(), 'UNIQUE constraint failed');
    }
}
