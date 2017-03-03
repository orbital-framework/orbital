<?php

/**
 * Observers
 *
 * Observer Sintax:
 * Observer::on('event', 'classs', 'method');
 *
 * Observer Examples:
 * Observer::on('userLogged', 'Observer_Logs', 'registerLogin')
 */

App::importFolder(APP. 'Config'. DS. 'Observers');