<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGowaWebhookJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GowaWebhookController extends Controller
{
    private const SIGNATURE_HEADER = 'X-Hub-Signature-256';

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Gowa webhook payload received', [
            'payload' => $payload,
        ]);

        if (! $this->hasValidSignature($request)) {
            Log::warning('Invalid GoWA webhook signature', [
                'signature' => $request->header(self::SIGNATURE_HEADER),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 401);
        }

        ProcessGowaWebhookJob::dispatch($payload);

        return response()->json([
            'success' => true,
        ]);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.gowa.webhook_secret');

        if (! is_string($secret) || $secret === '') {
            Log::error('GoWA webhook secret is not configured');

            return false;
        }

        $receivedSignature = $request->header(self::SIGNATURE_HEADER);

        if (! is_string($receivedSignature) || $receivedSignature === '') {
            Log::warning('Missing GoWA webhook signature header');

            return false;
        }

        $payload = $request->getContent();

        $expectedSignature = 'sha256='.hash_hmac(
            'sha256',
            $payload,
            $secret
        );

        return hash_equals(
            $expectedSignature,
            $receivedSignature
        );
    }
}
