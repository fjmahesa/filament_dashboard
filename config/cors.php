<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Daftarkan domain frontend kamu di sini, atau gunakan '*' untuk mengizinkan semua origin selama masa development
    'allowed_origins' => ['https://inmarco.id',
    'https://*.vercel.app', 'https://www.inmarco.id'], 

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-API-KEY',],

    'max_age' => 0,

    'supports_credentials' => false,

];