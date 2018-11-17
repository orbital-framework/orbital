<?php

/**
 * ORBITAL
 * https://github.com/orbital-framework
 */

// Require app
require_once '../app/bootstrap.php';

use \Orbital\Framework\App;
use \Orbital\Framework\Router;

// Load modules
// App::loadModule('\Acme\Core');
// App::loadModule('\Acme\Site');

// Run router
Router::runRequest();
