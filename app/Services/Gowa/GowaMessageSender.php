<?php

namespace App\Services\Gowa;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GowaMessageSender
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $username = null,
        private readonly ?string $password = null,
        private readonly ?string $deviceId = null,
    ) {}

    public static function fromConfig(): self
    {
        $baseUrl = (string) Config::get('services.gowa.base_url', '');

        if ($baseUrl === '') {
            throw new RuntimeException('GOWA base URL is not configured.');
        }

        $username = Config::get('services.gowa.username');
        $password = Config::get('services.gowa.password');
        $deviceId = Config::get('services.gowa.device_id');

        Log::info('Gowa message sender initialized', [
            'base_url' => $baseUrl,
            'username' => $username,
            'password' => $password,
            'device_id' => $deviceId,
        ]);

        return new self(
            baseUrl: $baseUrl,
            username: is_string($username) ? $username : null,
            password: is_string($password) ? $password : null,
            deviceId: is_string($deviceId) ? $deviceId : null,
        );
    }

    public function sendText(GowaSendMessageRequest $request): GowaSendMessageResponse
    {
        Log::info('Gowa message sender initialized', [
            'base_url' => $this->baseUrl,
            'username' => $this->username,
            'password' => $this->password,
            'device_id' => $this->deviceId,
        ]);

        $http = Http::withHeaders($this->headers());

        if ($this->username !== null && $this->password !== null) {
            $http = $http->withBasicAuth($this->username, $this->password);
        }

        $response = $http->post($this->url('/send/message'), $request->toArray());
        $response->throw();

        return GowaSendMessageResponse::from($response->json());
    }

    public function sendTextTo(
        string $phone,
        string $message,
        ?string $replyMessageId = null,
        array $mentions = [],
        ?bool $isForwarded = null,
        ?int $duration = null,
    ): GowaSendMessageResponse {
        return $this->sendText(GowaSendMessageRequest::text(
            phone: $this->normalizeRecipient($phone),
            message: $message,
            replyMessageId: $replyMessageId,
            mentions: $mentions,
            isForwarded: $isForwarded,
            duration: $duration,
        ));
    }

    private function headers(): array
    {
        if ($this->deviceId === null || $this->deviceId === '') {
            return [];
        }

        return [
            'X-Device-Id' => $this->deviceId,
        ];
    }

    private function url(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private function normalizeRecipient(string $recipient): string
    {
        if (str_contains($recipient, '@')) {
            return $recipient;
        }

        return $recipient . '@s.whatsapp.net';
    }
}
