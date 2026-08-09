<?php

declare(strict_types=1);

return [
    'name' => 'Victoria Universal',
    'base_url' => getenv('APP_URL') ?: '',
    'database' => dirname(__DIR__) . '/database/victoria.sqlite',
    'session_name' => 'victoria_session',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
];
