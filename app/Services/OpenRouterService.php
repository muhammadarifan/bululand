<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    private string $apiKey;

    private string $defaultModel;

    private int $timeout;

    private int $maxTokens;

    private const BASE_URL = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('openrouter.api_key', '');
        $this->defaultModel = config('openrouter.default_model', 'microsoft/phi-3-medium-128k-instruct');
        $this->timeout = config('openrouter.timeout', 30);
        $this->maxTokens = config('openrouter.max_tokens', 300);
    }

    /**
     * Kirim pesan ke OpenRouter dan dapatkan respons AI.
     *
     * @param  string  $message  Pesan dari user
     * @param  string  $systemPrompt  System prompt / instruksi untuk AI
     * @param  string|null  $model  Model ID (optional, default dari config)
     * @return string|null Respons teks dari AI, atau null jika gagal
     */
    public function chat(string $message, string $systemPrompt, ?string $model = null): ?string
    {
        if ($this->apiKey === '') {
            Log::warning('OpenRouter API key not configured');

            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => 'Bululand Bot',
                ])
                ->post(self::BASE_URL, [
                    'model' => $model ?? $this->defaultModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $message,
                        ],
                    ],
                    'max_tokens' => $this->maxTokens,
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                Log::error('OpenRouter API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            $reply = $data['choices'][0]['message']['content'] ?? null;

            if ($reply === null || trim($reply) === '') {
                Log::warning('OpenRouter returned empty response', [
                    'response' => $data,
                ]);

                return null;
            }

            return trim($reply);
        } catch (\Throwable $e) {
            Log::error('OpenRouter API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
