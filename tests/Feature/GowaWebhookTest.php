<?php

use App\Jobs\AutoReplyGowaWebhookJob;
use App\Jobs\ProcessGowaWebhookJob;
use App\Models\GowaWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config(['services.gowa.webhook_secret' => 'test-secret']);
});

function gowaHeaders(array $payload, string $secret = 'test-secret'): array
{
    return [
        'X-Hub-Signature-256' => 'sha256='.hash_hmac('sha256', json_encode($payload), $secret),
    ];
}

function gowaPayload(string $messageId = 'message-1'): array
{
    return [
        'event' => 'message.received',
        'device' => 'device-1',
        'chat_id' => 'chat-1',
        'from' => '6281234567890',
        'push_name' => 'Gowa User',
        'message' => [
            'id' => $messageId,
            'body' => 'Hello Gowa',
        ],
        'timestamp' => '2026-06-11T23:10:00+07:00',
    ];
}

test('webhook secret valid returns success', function (): void {
    Queue::fake();

    $payload = gowaPayload();

    $this->postJson('/webhook/gowa', $payload, gowaHeaders($payload))
        ->assertOk()
        ->assertJson(['success' => true]);

    Queue::assertPushed(ProcessGowaWebhookJob::class);
});

test('webhook secret invalid returns unauthorized', function (): void {
    Queue::fake();

    $payload = gowaPayload();

    $this->postJson('/webhook/gowa', $payload, gowaHeaders($payload, 'wrong-secret'))
        ->assertUnauthorized();

    Queue::assertNothingPushed();
});

test('job berhasil di-dispatch dengan payload webhook', function (): void {
    Queue::fake();

    $payload = gowaPayload('message-dispatch');

    $this->postJson('/webhook/gowa', $payload, gowaHeaders($payload))
        ->assertOk();

    Queue::assertPushed(ProcessGowaWebhookJob::class, function (ProcessGowaWebhookJob $job) use ($payload): bool {
        return $job->payload === $payload;
    });
});

test('auto reply job berhasil di-dispatch dengan payload webhook', function (): void {
    Queue::fake([AutoReplyGowaWebhookJob::class]);

    $payload = gowaPayload('message-auto-reply');

    $this->postJson('/webhook/gowa', $payload, gowaHeaders($payload))
        ->assertOk();

    Queue::assertPushed(AutoReplyGowaWebhookJob::class, function (AutoReplyGowaWebhookJob $job) use ($payload): bool {
        return $job->payload === $payload;
    });
});

test('duplicate event tidak diproses ulang', function (): void {
    $payload = gowaPayload('message-duplicate');

    $this->postJson('/webhook/gowa', $payload, gowaHeaders($payload))
        ->assertOk();
    $this->postJson('/webhook/gowa', $payload, gowaHeaders($payload))
        ->assertOk();

    $this->assertSame(1, GowaWebhookEvent::count());

    $event = GowaWebhookEvent::first();
    $this->assertSame('message-duplicate', $event?->event_id);
    $this->assertNotNull($event?->processed_at);
});
