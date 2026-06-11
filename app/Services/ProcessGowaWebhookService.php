<?php

namespace App\Services;

use App\DTO\GowaWebhookPayload;
use App\Models\GowaWebhookEvent;
use Illuminate\Database\QueryException;

final class ProcessGowaWebhookService
{
    public function handle(array $payload): void
    {
        $webhookPayload = GowaWebhookPayload::from($payload);
        $eventId = $webhookPayload->messageId();

        if ($eventId === null) {
            return;
        }

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

        $this->process($webhookPayload);
    }

    private function process(GowaWebhookPayload $payload): void
    {
        $payload->event();
        $payload->device();
        $payload->chatId();
        $payload->sender();
        $payload->pushName();
        $payload->message();
        $payload->timestamp();
    }

    private function isDuplicateEvent(QueryException $exception): bool
    {
        return in_array($exception->getCode(), [23000, '23000'], true)
            || str_contains($exception->getMessage(), 'Duplicate entry')
            || str_contains($exception->getMessage(), 'duplicate key value')
            || str_contains($exception->getMessage(), 'UNIQUE constraint failed');
    }
}
