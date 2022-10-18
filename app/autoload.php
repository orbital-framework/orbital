<?php
declare(strict_types=1);

/**
 * Autoload for classes
 * @param string $class
 * @return void
 */
spl_autoload_register(function(string $class): void {

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