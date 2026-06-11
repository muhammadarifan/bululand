<?php

namespace App\DTO;

use function data_get;

final class GowaWebhookPayload
{
    private function __construct(private readonly array $payload) {}

    public static function from(array $payload): self
    {
        return new self($payload);
    }

    public function event(): ?string
    {
        return $this->string('event');
    }

    public function device(): ?string
    {
        return $this->string('device');
    }

    public function chatId(): ?string
    {
        return $this->string('chat_id');
    }

    public function sender(): ?string
    {
        return $this->string('from');
    }

    public function pushName(): ?string
    {
        return $this->string('push_name');
    }

    public function message(): mixed
    {
        return data_get($this->payload, 'message');
    }

    public function timestamp(): ?string
    {
        return $this->string('timestamp');
    }

    public function messageId(): ?string
    {
        foreach ([
            'message.id',
            'message.message_id',
            'message.messageId',
            'messageId',
            'message_id',
            'id',
        ] as $key) {
            $value = data_get($this->payload, $key);

            if (is_scalar($value) && trim((string) $value) !== '') {
                return (string) $value;
            }
        }

        $fallback = array_filter([
            $this->event(),
            $this->device(),
            $this->chatId(),
            $this->sender(),
            $this->timestamp(),
        ], static fn (?string $value): bool => $value !== null && $value !== '');

        if ($fallback === []) {
            return null;
        }

        return 'gowa-'.hash('sha256', implode('|', $fallback));
    }

    public function all(): array
    {
        return $this->payload;
    }

    private function string(string $key): ?string
    {
        $value = data_get($this->payload, $key);

        if (! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        return (string) $value;
    }
}
