<?php

return [
    /*------------------------------------------------
    |           SMTP CONFIGURATION
    |-------------------------------------------------
    |
    |
    */
    'driver' => env('MAIL_DRIVER'),
    'host' => env('MAIL_HOST'),
    'port' => env('MAIL_PORT'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'encryption' => env('MAIL_ENCRYPTION'),
    'from' => env('MAIL_FROM')
];