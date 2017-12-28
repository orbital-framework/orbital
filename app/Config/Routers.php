<?php

/**
 * ROUTERS
 *
 * Sintax:
 * Router::set('method', 'regex/rule', 'callback', 'params', 'options');
 *
 * Examples:
 * Router::set(
 *    'GET', '/posts/([a-z]+)',
 *    'Controller_Blog@postAction', array('$2'));
 * Router::set(
 *    array('GET', 'POST'), '/contact',
 *    'Controller_Site@contactAction');
 * Router::set(
 *    'GET', '/list/medics.json',
 *    'Controller_Medics@listAction', array(),
 *    array('contentType' => 'application/json'));
 */

switch( ENVIRONMENT ){
    case 'development':
        $url = 'http://local.orbital.com';
    break;
    case 'staging':
        $url = 'http://staging.orbital.com';
    break;
    case 'production':
        $url = 'http://orbital.com';
    break;
}

App::set(array(
    'url' => $url
));

App::importFolder(APP. 'Config'. DS. 'Routers');