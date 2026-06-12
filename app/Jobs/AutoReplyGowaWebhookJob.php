<?php

namespace App\Jobs;

use App\DTO\GowaWebhookPayload;
use App\Services\AutoReplyGowaWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoReplyGowaWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly array $payload) {}

    public function handle(AutoReplyGowaWebhookService $service): void
    {
        Log::info('Auto reply job', [
            'payload' => $this->payload,
        ]);
        $service->handle(GowaWebhookPayload::from($this->payload));
    }
}
