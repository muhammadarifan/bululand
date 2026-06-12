<?php

use App\Services\Gowa\GowaMessageSender;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

test('gowa message sender mengirim pesan teks ke endpoint send message', function (): void {
    Http::fake(function () {
        return Http::response([
            'data' => [
                'message_id' => 'message-sent',
                'status' => 'sent',
            ],
        ], 200);
    });

    $sender = new GowaMessageSender(
        baseUrl: 'http://gowa.test',
        username: 'admin',
        password: 'secret',
        deviceId: 'device-1',
    );

    $response = $sender->sendTextTo(
        phone: '6281234567890',
        message: 'Halo',
        replyMessageId: 'message-incoming',
        mentions: ['@everyone'],
        isForwarded: true,
        duration: 86400,
    );

    Http::assertSent(function ($request): bool {
        return $request->url() === 'http://gowa.test/send/message'
            && $request->method() === 'POST'
            && $request->header('X-Device-Id') === ['device-1']
            && $request->header('Authorization') === ['Basic '.base64_encode('admin:secret')]
            && $request->data() === [
                'phone' => '6281234567890@s.whatsapp.net',
                'message' => 'Halo',
                'reply_message_id' => 'message-incoming',
                'mentions' => ['@everyone'],
                'is_forwarded' => true,
                'duration' => 86400,
            ];
    });

    expect($response->messageId)->toBe('message-sent');
    expect($response->status)->toBe('sent');
});
