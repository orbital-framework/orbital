<?php

if( version_compare(PHP_VERSION, '7.3') < 0 ):
    die('Orbital requires a newer version of PHP. Upgrade to version 7.3 or higher.');
endif;

// Environment
if( !defined('ENVIRONMENT') ){

    $environment = 'production';

    if( strpos($_SERVER['SERVER_NAME'], 'staging.') !== false ){
        $environment = 'staging';

    }elseif( strpos($_SERVER['SERVER_NAME'], 'local.') !== false
             OR strpos($_SERVER['SERVER_NAME'], 'dev.') !== false ){
        $environment = 'development';
    }

    define('ENVIRONMENT', $environment);

}

// Pathname separator
define('DS', DIRECTORY_SEPARATOR);

// Base dir
define('BASE', realpath(dirname(__DIR__)). DS);

// App dir
define('APP', BASE. 'app'. DS);

// Src dir
define('SRC', BASE. 'src'. DS);

// Logs dir
define('LOGS', BASE. 'logs'. DS);

// Public dir
define('WWW', BASE. 'www'. DS);

// Activate or deactivate error reporting in proper environment
if( ENVIRONMENT == 'development' ):

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL|E_NOTICE|E_STRICT);

else:

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL|E_STRICT);

endif;

// Logs
ini_set('log_errors', 1);
ini_set('error_log', LOGS. 'error.log');

// App autoload
if( file_exists(APP. 'autoload.php') ){
    require_once APP. 'autoload.php';
}