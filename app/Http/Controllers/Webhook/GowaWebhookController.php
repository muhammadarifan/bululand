<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGowaWebhookJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GowaWebhookController extends Controller
{
    private const SECRET_HEADER = 'X-Gowa-Signature';

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Gowa webhook payload received', ['payload' => $payload]);

        if (! $this->hasValidSecret($request)) {
            return response()->json(['success' => false], 401);
        }

        ProcessGowaWebhookJob::dispatch($payload);

        return response()->json([
            'success' => true,
        ]);
    }

    private function hasValidSecret(Request $request): bool
    {
        $secret = config('services.gowa.webhook_secret');

        if (! is_string($secret) || $secret === '') {
            return false;
        }

        $signature = (string) $request->header(self::SECRET_HEADER, '');

        return hash_equals($secret, $signature);
    }
}
