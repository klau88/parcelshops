<?php

return [
    'postnl' => env('POSTNL_KEY'),
    'gls' => [
        'username' => env('GLS_USERNAME'),
        'password' => env('GLS_PASSWORD'),
    ],
    'dhl' => [
        'token' => env('DHL_TOKEN'),
    ],
    'dpd' => [
        'delisId' => env('DPD_DELIS_ID'),
        'password' => env('DPD_PASSWORD'),
    ],
    'intrapost' => [
        'apiKey' => env('INTRAPOST_API_KEY'),
        'accountNumber' => env('INTRAPOST_ACCOUNT_NUMBER'),
    ]
];
