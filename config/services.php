<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ai_product' => [
        'provider' => env('AI_PRODUCT_PROVIDER', 'openai'),
        'api_key' => env('AI_PRODUCT_API_KEY', env('OPENAI_API_KEY')),
        'base_url' => env('AI_PRODUCT_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('AI_PRODUCT_MODEL', 'gpt-4o-mini'),
        'fetch_external' => (bool) env('AI_PRODUCT_FETCH_EXTERNAL', true),
        'timeout' => (int) env('AI_PRODUCT_TIMEOUT', 30),
    ],

];
