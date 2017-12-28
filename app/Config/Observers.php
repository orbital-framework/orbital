<?php

/**
 * Observers
 *
 * Sintax:
 * Observer::on('event', 'class', 'method');
 *
 * Examples:
 * Observer::on('userLogged', 'Observer_Logs', 'registerLogin')
 */

App::importFolder(APP. 'Config'. DS. 'Observers');