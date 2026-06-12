<?php

namespace App\Services;

use App\DTO\GowaWebhookPayload;
use App\Services\Gowa\GowaMessageSender;

use function data_get;

class AutoReplyGowaWebhookService
{
    public function __construct(private readonly GowaMessageSender $sender) {}

    public function handle(GowaWebhookPayload $payload): void
    {
        $reply = $this->replyForMessage($payload);

        if ($reply === null || $reply === '') {
            return;
        }

        $this->sendReply($payload, $reply);
    }

    protected function replyForMessage(GowaWebhookPayload $payload): ?string
    {
        $message = $payload->message();

        if (! is_array($message)) {
            return null;
        }

        $body = data_get($message, 'body');

        if (! is_string($body)) {
            return null;
        }

        if (str_contains(strtolower($body), 'halo')) {
            return 'Halo juga!';
        }

        return null;
    }

    protected function sendReply(GowaWebhookPayload $payload, string $reply): void
    {
        $chatId = $payload->chatId();

        if ($chatId === null) {
            return;
        }

        $this->sender->sendTextTo($chatId, $reply);
    }
}
