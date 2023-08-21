<?php

/**
 * Add and customize the following environment variables in your .env:
 *
 * RUTER_TOKEN_ENDPOINT    = "https://example.com/oauth2/token"
 * RUTER_CLIENT_ID         = example-id
 * RUTER_CLIENT_SECRET     = example-pass
 * RUTER_SCOPE             = "staging read"
 * RUTER_TRANSACTIONS_URL  = https://api.example.com/export?startdate=%s&enddate=%s
 */
return [
    // Client identificator
    'client' => [
        'id' => env('RUTER_CLIENT_ID', 'example-id'),
        'secret' => env('RUTER_CLIENT_SECRET', 'example-secret-password'),
    ],

    // URL used for oauth token authentication.
    'token_endpoint' => env('RUTER_TOKEN_ENDPOINT', 'https://example.com/oauth2/token'),

    // Access scope for our service
    'scope' => env('RUTER_SCOPE', 'staging read'),

    // Actual URL for retrieval of transaction data in CSV.
    'transactions_url' => env('RUTER_TRANSACTIONS_URL', 'https://api.example.com/export?startdate=%s&enddate=%s'),
];
