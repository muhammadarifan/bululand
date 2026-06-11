<?php

use App\Jobs\ProcessGowaWebhookJob;
use App\Models\GowaWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config(['services.gowa.webhook_secret' => 'test-secret']);
});

function gowaHeaders(string $signature = 'test-secret'): array
{
    return [
        'X-Gowa-Signature' => $signature,
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

    $this->postJson('/webhook/gowa', gowaPayload(), gowaHeaders())
        ->assertOk()
        ->assertJson(['success' => true]);

    Queue::assertPushed(ProcessGowaWebhookJob::class);
});

test('webhook secret invalid returns unauthorized', function (): void {
    Queue::fake();

    $this->postJson('/webhook/gowa', gowaPayload(), gowaHeaders('wrong-secret'))
        ->assertUnauthorized();

    Queue::assertNothingPushed();
});

test('job berhasil di-dispatch dengan payload webhook', function (): void {
    Queue::fake();

    $payload = gowaPayload('message-dispatch');

    $this->postJson('/webhook/gowa', $payload, gowaHeaders())
        ->assertOk();

    Queue::assertPushed(ProcessGowaWebhookJob::class, function (ProcessGowaWebhookJob $job) use ($payload): bool {
        return $job->payload === $payload;
    });
});

test('duplicate event tidak diproses ulang', function (): void {
    $payload = gowaPayload('message-duplicate');

    $this->postJson('/webhook/gowa', $payload, gowaHeaders())
        ->assertOk();
    $this->postJson('/webhook/gowa', $payload, gowaHeaders())
        ->assertOk();

    $this->assertSame(1, GowaWebhookEvent::count());

    $event = GowaWebhookEvent::first();
    $this->assertSame('message-duplicate', $event?->event_id);
    $this->assertNotNull($event?->processed_at);
});
