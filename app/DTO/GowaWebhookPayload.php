<?php

namespace App\DTO;

use function data_get;

final class GowaWebhookPayload
{
    private function __construct(private readonly array $payload) {}

    // {
    //     "device_id": "6283891774885@s.whatsapp.net",
    //     "event": "message",
    //     "payload": {
    //       "body": "halo",
    //       "chat_id": "6282336066323@s.whatsapp.net",
    //       "chat_lid": "158158009962737@lid",
    //       "from": "6282336066323@s.whatsapp.net",
    //       "from_lid": "158158009962737@lid",
    //       "from_name": "Muhammad Arifan L",
    //       "id": "3B5F101EDA6A8E36D107",
    //       "is_from_me": false,
    //       "timestamp": "2026-06-19T04:29:04Z"
    //     }
    //   }

    public static function from(array $payload): self
    {
        return new self($payload);
    }

    public function event(): ?string
    {
        return $this->string('event');
    }

    public function deviceId(): ?string
    {
        return $this->string('device_id');
    }

    public function chatId(): ?string
    {
        return $this->string('payload.chat_id');
    }

    public function sender(): ?string
    {
        return $this->string('payload.from');
    }

    public function pushName(): ?string
    {
        return $this->string('payload.from_name');
    }

    public function message(): mixed
    {
        return data_get($this->payload, 'payload.body');
    }

    public function timestamp(): ?string
    {
        return $this->string('payload.timestamp');
    }

    public function messageId(): ?string
    {
        return $this->string('payload.id');
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
