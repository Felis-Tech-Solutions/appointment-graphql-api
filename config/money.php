<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel money
     |--------------------------------------------------------------------------
     */
    'locale'           => config('app.locale', 'nl_NL'),
    'defaultCurrency' => 'EUR',
    'currencies'       => [
        'iso' => [
            'EUR',
        ],
    ],
];