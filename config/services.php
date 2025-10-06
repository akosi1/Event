<?php

return [

  /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Site Key
    |--------------------------------------------------------------------------
    |
    | The site key for reCAPTCHA v3
    |
    */
    'site_key' => env('6LeVhOArAAAAAOniB4kol7R10b0UkjZ9X7UhkPdm', ''),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key for reCAPTCHA v3
    |
    */
    'secret_key' => env('6LeVhOArAAAAAGB9JbjWQfQcVKtrpxZZ9RiNiiBH', ''),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Minimum Score
    |--------------------------------------------------------------------------
    |
    | The minimum score required to pass reCAPTCHA validation (0.0 - 1.0)
    | Default: 0.5
    |
    */
    'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

];
