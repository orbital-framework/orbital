<?php

/**
 * ROUTERS
 *
 * Router Sintax:
 * Router::set('method', 'regex/rule', 'controller', 'method', [array('paramN', ...)], ['contentType']);
 *
 * Router Segments:
 * To send segment parameters to the URL method, set values in the format "$[segment]"
 * Eg: $2 will pass the second segment of the url:
 * URL = page/123 | Segment 1 = page | Segment 2 = 123
 * Segments will only be accepted and translated if they start with "$" followed by a number
 *
 * Router Examples:
 * Router::set('GET', '/posts/([a-z]+)', 'Controller_Blog', 'postAction', array('$2'));
 * Router::set( array('GET', 'POST'), '/contact', 'Controller_Site', 'contactAction');
 * Router::set('GET', '/list/medics.json', 'Controller_Medics', 'listAction', NULL, 'application/json');
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