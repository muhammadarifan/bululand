<?php

use App\DTO\GowaWebhookPayload;
use App\Services\AutoReplyGowaWebhookService;
use App\Services\Gowa\GowaMessageSender;
use App\Services\Gowa\GowaSendMessageResponse;

test('auto reply service membalas halo', function (): void {
    $payload = GowaWebhookPayload::from([
        'payload' => [
            'body' => 'halo',
            'chat_id' => 'chat-1',
        ],
    ]);

    $sender = new class('http://gowa.test', 'admin', 'secret', 'device-1') extends GowaMessageSender
    {
        public array $sent = [];

        public function sendTextTo(
            string $phone,
            string $message,
            ?string $replyMessageId = null,
            array $mentions = [],
            ?bool $isForwarded = null,
            ?int $duration = null,
        ): GowaSendMessageResponse {
            $this->sent[] = [
                'phone' => $phone,
                'message' => $message,
            ];

            return new GowaSendMessageResponse('message-sent', 'sent', []);
        }
    };

    $service = new AutoReplyGowaWebhookService($sender);

    $service->handle($payload);

    expect($sender->sent)->toBe([
        [
            'phone' => 'chat-1',
            'message' => 'Halo juga!',
        ],
    ]);
});

test('auto reply service menjalankan pengkondisian balasan', function (): void {
    $payload = GowaWebhookPayload::from([
        'device_id' => 'device-1',
        'payload' => [
            'body' => 'Halo',
            'chat_id' => 'chat-1',
            'from' => '6281234567890',
        ],
    ]);

    $sender = new class('http://gowa.test', 'admin', 'secret', 'device-1') extends GowaMessageSender
    {
        public array $sent = [];

        public function sendTextTo(
            string $phone,
            string $message,
            ?string $replyMessageId = null,
            array $mentions = [],
            ?bool $isForwarded = null,
            ?int $duration = null,
        ): GowaSendMessageResponse {
            $this->sent[] = [
                'phone' => $phone,
                'message' => $message,
                'replyMessageId' => $replyMessageId,
                'mentions' => $mentions,
                'isForwarded' => $isForwarded,
                'duration' => $duration,
            ];

            return new GowaSendMessageResponse('message-sent', 'sent', []);
        }
    };

    $service = new class($sender) extends AutoReplyGowaWebhookService
    {
        protected function replyForMessage(GowaWebhookPayload $payload): ?string
        {
            return $payload->sender() === '6281234567890' ? 'Auto reply' : null;
        }

        protected function sendReply(GowaWebhookPayload $payload, string $reply): void
        {
            parent::sendReply($payload, $reply);

            $GLOBALS['gowa_auto_reply'] = [
                $payload->sender(),
                $payload->chatId(),
                $payload->deviceId(),
                $reply,
            ];
        }
    };

    $service->handle($payload);

    expect($GLOBALS['gowa_auto_reply'])->toBe([
        '6281234567890',
        'chat-1',
        'device-1',
        'Auto reply',
    ]);

    expect($sender->sent)->toBe([
        [
            'phone' => 'chat-1',
            'message' => 'Auto reply',
            'replyMessageId' => null,
            'mentions' => [],
            'isForwarded' => null,
            'duration' => null,
        ],
    ]);

    unset($GLOBALS['gowa_auto_reply']);
});

test('auto reply service menampilkan menu', function (): void {
    $payload = GowaWebhookPayload::from([
        'payload' => [
            'body' => '/menu',
            'chat_id' => 'chat-1',
            'from' => '6281234567890',
        ],
    ]);

    $sender = new class('http://gowa.test', 'admin', 'secret', 'device-1') extends GowaMessageSender
    {
        public array $sent = [];

        public function sendTextTo(
            string $phone,
            string $message,
            ?string $replyMessageId = null,
            array $mentions = [],
            ?bool $isForwarded = null,
            ?int $duration = null,
        ): GowaSendMessageResponse {
            $this->sent[] = [
                'phone' => $phone,
                'message' => $message,
            ];

            return new GowaSendMessageResponse('message-sent', 'sent', []);
        }
    };

    $service = new AutoReplyGowaWebhookService($sender);

    $service->handle($payload);

    expect($sender->sent)->toHaveCount(1);
    expect($sender->sent[0]['message'])->toContain('MENU BOT GOWA');
    expect($sender->sent[0]['message'])->toContain('Cek Iuran Event');
    expect($sender->sent[0]['message'])->toContain('Laporan Keuangan');
});
