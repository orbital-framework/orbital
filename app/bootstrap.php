<?php

if( version_compare(PHP_VERSION, '7.3') < 0 ):
    die('Orbital requires a newer version of PHP. Upgrade to version 7.3 or higher.');
endif;

// Pathname separator
define('DS', DIRECTORY_SEPARATOR);

// Base dir
define('BASE', realpath(dirname(__DIR__)). DS);

// App dir
define('APP', BASE. 'app'. DS);

// Src dir
define('SRC', BASE. 'src'. DS);

// Public dir
define('WWW', BASE. 'www'. DS);

// Environment variables
if( file_exists(APP. 'env.php') ):
    \Orbital\Env\Env::load(APP. 'env.php');
endif;

if( file_exists(APP. 'env.local.php') ):
    \Orbital\Env\Env::load(APP. 'env.local.php');
endif;

// Errors
ini_set('log_errors', 1);

if( \Orbital\Env\Env::get('APP_ENVIRONMENT') == 'development' ):

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL|E_NOTICE|E_STRICT);

else:

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL|E_STRICT);

endif;

// Vendor autoload
if( file_exists(BASE. 'vendor/autoload.php') ):
    require_once BASE. 'vendor/autoload.php';
endif;

// App autoload
if( file_exists(APP. 'autoload.php') ):
    require_once APP. 'autoload.php';
endif;

// App kernel
require_once APP. 'kernel.php';