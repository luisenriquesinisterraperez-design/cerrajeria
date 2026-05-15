<?php
declare(strict_types=1);

return [
    /* 
     * FORZAMOS DEBUG TRUE 
     * Esto nos permitirá ver el error real (Reason) en la pantalla de "Missing Connection"
     */
    'debug' => true,

    'App' => [
        'namespace' => 'App',
        'encoding' => 'UTF-8',
        'defaultLocale' => 'es_ES',
        'defaultTimezone' => 'UTC',
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

            /* 
             * Buscamos las variables en todas partes ($_SERVER, $_ENV y env())
             */
            'host' => $_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? $_SERVER['DATABASE_HOST'] ?? env('DATABASE_HOST', '127.0.0.1'),
            'port' => $_SERVER['DB_PORT'] ?? $_ENV['DB_PORT'] ?? $_SERVER['DATABASE_PORT'] ?? env('DATABASE_PORT', 3306),
            'username' => $_SERVER['DB_USER'] ?? $_ENV['DB_USER'] ?? $_SERVER['DATABASE_USER'] ?? env('DATABASE_USER', 'my_app'),
            'password' => $_SERVER['DB_PASSWORD'] ?? $_ENV['DB_PASSWORD'] ?? $_SERVER['DATABASE_PASSWORD'] ?? env('DATABASE_PASSWORD', 'secret'),
            'database' => $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? $_SERVER['DB_DATABASE'] ?? env('DATABASE_NAME', 'my_app'),
            
            /* Si tienes una DATABASE_URL configurada en EasyPanel, úsala */
            'url' => $_SERVER['DATABASE_URL'] ?? $_ENV['DATABASE_URL'] ?? $_SERVER['DB_URL'] ?? env('DATABASE_URL', null),

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
