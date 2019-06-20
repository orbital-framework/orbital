<?php

/**
 * ORBITAL
 * https://github.com/orbital-framework
 */

// Vendor autoload
require_once __DIR__. '/../vendor/autoload.php';

// Require app
require_once __DIR__. '/../app/bootstrap.php';

// Run router
Orbital\Framework\Router::runRequest();