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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bitcoin' => [
        'rpc_url' => env('BITCOIN_RPC_URL'),
        'rpc_user' => env('BITCOIN_RPC_USER'),
        'rpc_password' => env('BITCOIN_RPC_PASSWORD'),
    ],

    'blockio' => [
        'api_key' => env('BLOCKIO_API_KEY'),
        'pin' => env('BLOCKIO_PIN'),
        'version' => 2,
        // Montant envoyé à chaque adresse d'électeur pour couvrir les frais de vote
        'fee_amount' => env('BLOCKIO_FEE_AMOUNT', '0.0001'),
    ],

    'solana' => [
        'rpc_url' => env('SOLANA_RPC_URL', 'https://api.devnet.solana.com'),
    ],

    'cryptoapis' => [
        'api_key' => env('CRYPTOAPIS_API_KEY'),
        'base_url' => env('CRYPTOAPIS_BASE_URL', 'https://rest.cryptoapis.io'),
    ],

    'veriff' => [
        'host' => env('VERIFF_HOST', 'https://stationapi.veriff.com'),
        'api_key' => env('VERIFF_API_KEY'),
        'secret' => env('VERIFF_SECRET_KEY'),
    ],

];
