<?php

return [
    'access_mode' => env('DASHBOARD_ACCESS_MODE', env('APP_ENV') === 'local' ? 'disabled' : 'token'),
    'api_token' => env('DASHBOARD_API_TOKEN'),
    'basic_user' => env('DASHBOARD_BASIC_USER'),
    'basic_pass_hash' => env('DASHBOARD_BASIC_PASS_HASH'),
];
