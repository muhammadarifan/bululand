<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk OpenRouter AI API.
    | Daftar model gratis: https://openrouter.ai/models?order=newest&type=free
    |
    */

    'api_key' => env('OPENROUTER_API_KEY'),

    'default_model' => env('OPENROUTER_MODEL', 'microsoft/phi-3-medium-128k-instruct'),

    'timeout' => 30,

    'max_tokens' => 300,

    'rate_limit' => [
        /*
         * Total maksimal AI reply per jam (semua user)
         */
        'max_per_hour' => (int) env('OPENROUTER_RATE_LIMIT', 20),

        /*
         * Maksimal AI reply per user per jam
         */
        'max_per_user_per_hour' => (int) env('OPENROUTER_USER_RATE_LIMIT', 5),
    ],
];
