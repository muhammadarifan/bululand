<?php

use App\DTO\GowaWebhookPayload;
use App\Services\ProcessGowaWebhookService;

test('normalizePayload mengubah nested structure menjadi flat', function (): void {
    $nestedPayload = [
        'reply' => null,
        'payload' => [
            'device_id' => '6283891774885@s.whatsapp.net',
            'event' => 'message',
            'payload' => [
                'body' => 'halo',
                'chat_id' => '6282336066323@s.whatsapp.net',
                'chat_lid' => '158158009962737@lid',
                'from' => '6282336066323@s.whatsapp.net',
                'from_lid' => '158158009962737@lid',
                'from_name' => 'Muhammad Arifan L',
                'id' => '3B2B07097D21ED0CC97C',
                'is_from_me' => false,
                'timestamp' => '2026-06-19T03:05:23Z',
            ],
        ],
    ];

    // Buat service menggunakan reflection untuk akses normalizePayload
    $service = new ProcessGowaWebhookService;
    $reflection = new ReflectionMethod($service, 'normalizePayload');
    $normalized = $reflection->invoke($service, $nestedPayload);

    expect($normalized)->toBe([
        'event' => 'message',
        'device' => '6283891774885@s.whatsapp.net',
        'from' => '6282336066323@s.whatsapp.net',
        'from_name' => 'Muhammad Arifan L',
        'from_lid' => '158158009962737@lid',
        'chat_id' => '6282336066323@s.whatsapp.net',
        'chat_lid' => '158158009962737@lid',
        'is_from_me' => false,
        'message' => [
            'body' => 'halo',
            'chat_id' => '6282336066323@s.whatsapp.net',
            'chat_lid' => '158158009962737@lid',
            'from' => '6282336066323@s.whatsapp.net',
            'from_lid' => '158158009962737@lid',
            'from_name' => 'Muhammad Arifan L',
            'id' => '3B2B07097D21ED0CC97C',
            'is_from_me' => false,
            'timestamp' => '2026-06-19T03:05:23Z',
        ],
    ]);
});

test('normalizePayload mengembalikan flat payload apa adanya', function (): void {
    $flatPayload = [
        'event' => 'message',
        'device' => 'device-1',
        'from' => '6281234567890',
        'chat_id' => 'chat-1',
        'message' => [
            'id' => 'msg-1',
            'body' => 'Hello',
        ],
    ];

    $service = new ProcessGowaWebhookService;
    $reflection = new ReflectionMethod($service, 'normalizePayload');
    $normalized = $reflection->invoke($service, $flatPayload);

    expect($normalized)->toBe($flatPayload);
});

test('nilai dari GowaWebhookPayload setelah normalisasi untuk nested payload', function (): void {
    $nestedPayload = [
        'reply' => null,
        'payload' => [
            'device_id' => '6283891774885@s.whatsapp.net',
            'event' => 'message',
            'payload' => [
                'body' => '/menu',
                'chat_id' => '6282336066323@s.whatsapp.net',
                'from' => '6282336066323@s.whatsapp.net',
                'from_name' => 'Muhammad Arifan L',
                'id' => '3B8DDB4752DC58B2FE5A',
                'is_from_me' => false,
                'timestamp' => '2026-06-19T03:02:18Z',
            ],
        ],
    ];

    $service = new ProcessGowaWebhookService;
    $reflection = new ReflectionMethod($service, 'normalizePayload');
    $normalized = $reflection->invoke($service, $nestedPayload);

    $dto = GowaWebhookPayload::from($normalized);

    expect($dto->event())->toBe('message');
    expect($dto->chatId())->toBe('6282336066323@s.whatsapp.net');
    expect($dto->sender())->toBe('6282336066323@s.whatsapp.net');
    expect($dto->messageId())->toBe('3B8DDB4752DC58B2FE5A');

    $message = $dto->message();
    expect($message)->toBeArray();
    expect($message['body'])->toBe('/menu');
});
