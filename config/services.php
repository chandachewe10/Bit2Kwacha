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

    'opennode' => [
        'api_key' => env('OPENNODE_API_KEY'),
        'api_key_withdrawal' => env('OPENNODE_WITHDRAWAL_API_KEY'),
        'exchange_rates_token' => env('EXCHANGE_RATES_TOKEN'),
        'balance_check_token' => env('BALANCE_CHECK_TOKEN'),
        'base_uri' => env('OPENNODE_BASE_URI'),
        'base_uri_withdrawal' => env('OPENNODE_BASE_URI_FOR_WITHDRAWAL'),
        'mobile_money' => env('OPENNODE_WEBHOOK_MOBILE_MONEY'),
        'bank_transfer' => env('OPENNODE_WEBHOOK_BANK_TRANSFER'),
        'withdrawal' => env('OPENNODE_WEBHOOK_WITHDRAWAL_CALLBACK')
    ],

    'lenco' => [
        'token' => env('LENCO_TOKEN'),
        'base_uri' => env('LENCO_BASE_URI'),
        'wallet_uuid' => env('LENCO_WALLET_UUID'),

    ],

    'whatsapp' => [
        'version' => env('WHATSAPP_VERSION', 'v19.0'), // WhatsApp Business API version
        'business_phone_number_id' => env('WHATSAPP_BUSINESS_PHONE_NUMBER_ID'), // Your WhatsApp Business Phone Number ID
        'token' => env('WHATSAPP_TOKEN'), // WhatsApp Business API Access Token
        'base_uri' => env('WHATSAPP_BASE_URI', 'https://graph.facebook.com'), // WhatsApp Business API base URL
    ],

    'bitcoin' => [
        'conversion_rate' => env('BITCOIN_CONVERSION_RATE', 0.023), // SAT to ZMW rate (1 SAT = 0.023 ZMW)
        'service_fee_rate' => env('BITCOIN_SERVICE_FEE_RATE', 0.15), // 15% service fee
        'buy_network_fee' => env('BITCOIN_BUY_NETWORK_FEE', 5), // Network fee for buying (ZMW)
        'sell_network_fee' => env('BITCOIN_SELL_NETWORK_FEE', 400), // Network fee for selling (SATS)
        // When true, front-end will use OpenNode live exchange rates; when false, it will use BITCOIN_CONVERSION_RATE only
        'use_open_node_exchange_rate' => env('USE_OPENNODE_EXCHANGE_RATE', false),
    ],

];
