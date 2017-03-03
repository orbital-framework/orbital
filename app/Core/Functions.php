<?php

/**
 * Autoload for classes
 * @param string $class
 * @return void
 */
function __autoload($class){

	$file = str_replace('_', DS, $class). '.php';
	
	foreach (array(
		APP. 'Classes'. DS. $file,
		APP. 'Classes/Model'. DS. $file,
		APP. 'Classes/Libraries'. DS. $file,
		VENDOR. $file
	) as $file) {

		if( is_file($file) ){
			require_once $file;
		}

	}

}
spl_autoload_register('__autoload');

/**
 * Retrieve translation
 * @param string $string
 * @param mixed $scope
 * @param mixed $language
 * @return string
 */
function __($string, $scope = NULL, $language = NULL){
	return I18n::get($string, $scope, $language);
}

/**
 * Show translation
 * @param string $string
 * @param mixed $scope
 * @param mixed $language
 * @return void
 */
function _e($string, $scope = NULL, $language = NULL){
	echo __($string, $scope, $language);
}