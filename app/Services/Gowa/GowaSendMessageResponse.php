<?php

namespace App\Services\Gowa;

use function data_get;

final readonly class GowaSendMessageResponse
{
    public function __construct(
        public ?string $messageId,
        public string $status,
        public array $raw,
    ) {}

    public static function from(?array $payload): self
    {
        $payload ??= [];
        $data = data_get($payload, 'data', $payload);

        $messageId = data_get($data, 'message_id')
            ?? data_get($data, 'messageId')
            ?? data_get($payload, 'message_id')
            ?? data_get($payload, 'messageId');

        $status = (string) (data_get($data, 'status') ?? data_get($payload, 'status') ?? 'sent');

        return new self(
            messageId: is_string($messageId) ? $messageId : null,
            status: $status,
            raw: $payload,
        );
    }
}
