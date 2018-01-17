<?php

if( version_compare(PHP_VERSION, '5.6') < 0 ):
	die('Orbital requires a newer version of PHP. Upgrade to version 5.6 or higher.');
endif;

// Enviroment
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
define('BASE', realpath(dirname( dirname(__FILE__) )). DS);

// App dir
define('APP', BASE. 'app'. DS);

// Src dir
define('SRC', BASE. 'src'. DS);

// Vendor dir
define('VENDOR', BASE. 'vendor'. DS);

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
	error_reporting(0);

endif;

// Logs
ini_set('log_errors', 1);
ini_set('error_log', LOGS. 'error.log');

// App core
require_once APP. 'Core'. DS. 'App.php';

App::importFile(APP. 'Core' , array(
	'Functions',
	'Object',
	'Observer',
	'Router',
	'Header',
	'View'
));

// App config
App::importFolder(APP. 'Config');

// Vendor autoload
if( file_exists(VENDOR. 'autoload.php') ){
	require_once VENDOR. 'autoload.php';
}