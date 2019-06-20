<?php

/**
 * Autoload for classes
 * @param string $class
 * @return void
 */
spl_autoload_register(function($class){

    $file = str_replace('_', DS, $class). '.php';
    $file = str_replace('\\', DS, $file);

    foreach (array(
        SRC. $file
    ) as $file) {

        if( is_file($file) ){
            require_once $file;
        }

    }

});