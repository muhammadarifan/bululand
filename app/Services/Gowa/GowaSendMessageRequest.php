<?php

namespace App\Services\Gowa;

final readonly class GowaSendMessageRequest
{
    public function __construct(
        public string $phone,
        public string $message,
        public ?string $replyMessageId = null,
        public array $mentions = [],
        public ?bool $isForwarded = null,
        public ?int $duration = null,
    ) {}

    public static function text(
        string $phone,
        string $message,
        ?string $replyMessageId = null,
        array $mentions = [],
        ?bool $isForwarded = null,
        ?int $duration = null,
    ): self {
        return new self(
            phone: $phone,
            message: $message,
            replyMessageId: $replyMessageId,
            mentions: $mentions,
            isForwarded: $isForwarded,
            duration: $duration,
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'message' => $this->message,
        ];

        if ($this->replyMessageId !== null) {
            $payload['reply_message_id'] = $this->replyMessageId;
        }

        if ($this->mentions !== []) {
            $payload['mentions'] = $this->mentions;
        }

        if ($this->isForwarded !== null) {
            $payload['is_forwarded'] = $this->isForwarded;
        }

        if ($this->duration !== null) {
            $payload['duration'] = $this->duration;
        }

        return $payload;
    }
}
