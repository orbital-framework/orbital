<?php

/**
 * Autoload for classes
 * @param string $class
 * @return void
 */
function __autoload($class){

    $file = str_replace('_', DS, $class). '.php';
    $file = str_replace('\\', DS, $file);

    foreach (array(
        SRC. $file,
        APP. 'Libraries'. DS. $file
    ) as $file) {

        if( is_file($file) ){
            require_once $file;
        }

    }

}
spl_autoload_register('__autoload');