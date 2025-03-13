<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],  // Allows all HTTP methods

    'allowed_origins' => [ 
        'https://blitxpress.com',
        'http://blitxpress.com',
        'https://www.blitxpress.com',
        'http://www.blitxpress.com',
        '*.blitxpress.com',  // Allows all subdomains
    ],

    'allowed_origins_patterns' => ['/^https?:\/\/(.+\.)?blitxpress\.com$/'], // Regex pattern for dynamic subdomains

    'allowed_headers' => ['*'],  // Allows all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,  // Enable if using cookies or authentication with API
];
