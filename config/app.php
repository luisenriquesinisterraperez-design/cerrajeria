<?php
declare(strict_types=1);

return [
    'debug' => true,

    'App' => [
        'namespace' => 'App',
        'encoding' => env('APP_ENCODING', 'UTF-8'),
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'UTC'),
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        'fullBaseUrl' => 'https://cerrajeria.stokmaster.com.co',
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [ROOT . DS . 'plugins' . DS],
            'templates' => [ROOT . DS . 'templates' . DS],
            'locales' => [RESOURCES . 'locales' . DS],
        ],
    ],

    'Security' => [
        'salt' => env('SECURITY_SALT', '70214f44059424856f772584149021e145789632145874563214589632145874'),
    ],

    'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'timezone' => 'UTC',

            'host' => $_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? $_SERVER['DATABASE_HOST'] ?? $_ENV['DATABASE_HOST'] ?? '127.0.0.1',
            'port' => $_SERVER['DB_PORT'] ?? $_ENV['DB_PORT'] ?? $_SERVER['DATABASE_PORT'] ?? $_ENV['DATABASE_PORT'] ?? 3306,
            'username' => $_SERVER['DB_USER'] ?? $_ENV['DB_USER'] ?? $_SERVER['DATABASE_USER'] ?? $_ENV['DATABASE_USER'] ?? 'my_app',
            'password' => $_SERVER['DB_PASSWORD'] ?? $_ENV['DB_PASSWORD'] ?? $_SERVER['DATABASE_PASSWORD'] ?? $_ENV['DATABASE_PASSWORD'] ?? 'secret',
            'database' => $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? $_SERVER['DB_DATABASE'] ?? $_ENV['DB_DATABASE'] ?? $_SERVER['DATABASE_NAME'] ?? $_ENV['DATABASE_NAME'] ?? 'my_app',

            'encoding' => 'utf8mb4',
            'cacheMetadata' => true,
            'log' => false,
            'quoteIdentifiers' => true,
        ],
    ],

    'Session' => [
        'defaults' => 'php',
    ],

    'Log' => [
        'debug' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'debug',
            'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'error',
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
    ],
];
