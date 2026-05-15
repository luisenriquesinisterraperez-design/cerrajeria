<?php
declare(strict_types=1);

return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => true,

    /*
     * Configure basic information about the application.
     *
     * - namespace - The namespace to find app classes under.
     * - defaultLocale - The default locale for your application.
     * - defaultTimezone - The default timezone for your application.
     * - encoding - The encoding used for HTML + database connections.
     * - base - The base directory the app resides in. If false this
     *   will be auto-detected.
     * - dir - Name of app directory.
     * - webroot - The webroot directory.
     * - wwwRoot - The file path to webroot.
     * - baseUrl - To configure CakePHP to *not* use mod_rewrite and to
     *   use CakePHP short URLs, you can set the base directory here.
     * - fullBaseUrl - The full base URL for all absolute links.
     * - imageBaseUrl - Web path to public images directory.
     * - cssBaseUrl - Web path to public css directory.
     * - jsBaseUrl - Web path to public js directory.
     * - paths - Configure paths for templates, layouts etc.
     */
    'App' => [
        'namespace' => 'App',
        'encoding' => env('APP_ENCODING', 'UTF-8'),
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'UTC'),
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        //'baseUrl' => env('SCRIPT_NAME'),
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

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT'),
    ],

    /*
     * Apply timezone setting from config into PHP.
     */
    'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'timezone' => 'UTC',

            'host' => env('DATABASE_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('DATABASE_PORT', env('DB_PORT', 3306)),
            'username' => env('DATABASE_USER', env('DB_USER', 'my_app')),
            'password' => env('DATABASE_PASSWORD', env('DB_PASSWORD', 'secret')),
            'database' => env('DATABASE_NAME', env('DB_DATABASE', env('DB_NAME', 'my_app'))),
            'url' => env('DATABASE_URL', env('DB_URL', null)),

            'encoding' => 'utf8mb4',
            'cacheMetadata' => true,
            'log' => false,
            'quoteIdentifiers' => true,
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'timezone' => 'UTC',
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'test_myapp',
            //'schema' => 'myapp',
            'url' => env('NODE_ENV') === 'test' ? env('DATABASE_TEST_URL') : null,
            'encoding' => 'utf8mb4',
            'cacheMetadata' => true,
            'quoteIdentifiers' => true,
            'log' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
        ],
    ],

    /*
     * Session configuration.
     *
     * Contains an array of settings to use for session configuration. The
     * `defaults` key is used to define a default preset to use for sessions, any
     * settings declared here will override the settings of the default config.
     *
     * ## Options
     *
     * - `defaults` - The default configuration set to use as a basis for your session.
     *    There are four built-in options: 'php', 'cake', 'cache', 'database'.
     * - `handler` - Can be an array of callbacks to be used with `session_set_save_handler()`.
     *    Can be used to set custom session save handlers.
     * - `ini` - An associative array of additional 'session.*` ini values to set.
     *
     * The built-in defaults are:
     *
     * - 'php' - Uses settings defined in your php.ini.
     * - 'cake' - Saves session files in CakePHP's /tmp directory.
     * - 'cache' - Use the Cache class to save sessions.
     * - 'database' - Use the database to save sessions.
     *
     * To set a specific session JSESSIONID cookie name:
     *
     * ```
     * 'Session' => [
     *     'defaults' => 'php',
     *     'ini' => [
     *         'session.cookie_name' => 'JSESSIONID',
     *     ],
     * ],
     * ```
     *
     * To use the database session handler, run `bin/cake migrations migrate` after
     * creating the migration with `bin/cake migrations create CreateSessions`.
     */
    'Session' => [
        'defaults' => 'php',
    ],

    /*
     * Email configuration.
     *
     * Host and password configuration in these connectors are 
     * commonly overridden in app_local.php
     */
    'EmailTransport' => [
        'default' => [
            'className' => 'Mail',
            // The following keys are used in SMTP transports
            'host' => 'localhost',
            'port' => 25,
            'timeout' => 30,
            'username' => null,
            'password' => null,
            'client' => null,
            'tls' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],

    /*
     * Email delivery profiles
     *
     * Delivery profiles allow you to predefine all email settings so you
     * can easily send messages from anywhere in your application.
     */
    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => 'you@localhost',
            //'charset' => 'utf-8',
            //'headerCharset' => 'utf-8',
        ],
    ],

    /*
     * Log configuration
     *
     * In the case of files, levels are the levels of log messages
     * to be logged by the scope, and file is the file to log them to.
     */
    'Log' => [
        'debug' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'debug',
            'url' => env('LOG_DEBUG_URL', null),
            'scopes' => false,
            'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'error',
            'url' => env('LOG_ERROR_URL', null),
            'scopes' => false,
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
        // To enable separate 'queries' logging, uncomment the following:
        /*
        'queries' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'queries',
            'url' => env('LOG_QUERIES_URL', null),
            'scopes' => ['cake.database.queries'],
        ],
        */
    ],
];
