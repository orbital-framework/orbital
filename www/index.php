<?php

/**
 * ORBITAL
 * https://github.com/orbital-framework
 */

// Require app
require_once __DIR__. '/../app/bootstrap.php';

// Run router
Orbital\Framework\Router::runRequest();