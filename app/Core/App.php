<?php

abstract class App{

	/**
	 * Singleton instances
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Configs
	 * @var array
	 */
	private static $config = array();

	/**
	 * Import all files from folder
	 * @param string|array $directory
	 * @param string $extension
	 * @return void
	 */
	public static function importFolder($directory, $extension = '.php'){

		if( is_array($directory) ){

			foreach( $directory as $key => $value ){
				self::importFolder($value, $extension);
			}

			return;
		}

		$directory = rtrim($directory, DS);

		// Include files from directory
		foreach( glob($directory. DS. '*'. $extension) as $file ){
			if( file_exists($file) ){
				require_once $file;
			}
		}

	}

	/**
	 * Import files
	 * @param string $directory
	 * @param string|array $file
	 * @param string $extension
	 * @return void
	 */
	public static function importFile($directory, $file, $extension = '.php'){

		if( is_array($file) ){

			foreach( $file as $key => $value ){
				self::importFile($directory, $value, $extension);
			}

			return;
		}

		$directory = rtrim($directory, DS);

		// Include the file
		if( file_exists($directory. DS. $file. $extension) ){
			require_once $directory. DS. $file. $extension;
		}

	}

	/**
	 * Set config data
	 * @param string|array $key
	 * @param string $value
	 * @return mixed
	 */
	public static function set($key, $value = NULL){

		if( is_array($key) ){
			return self::$config = array_merge(self::$config, $key);
		}

		return self::$config[$key] = $value;
	}

	/**
	 * Remove config data
	 * @param string|array $key
	 * @return void
	 */
	public static function delete($key){

		if( is_array($key) ){

			foreach( $key as $item ){
				self::delete($item);
			}

			return;
		}

		unset(self::$config[$key]);
	}

	/**
	 * Retrieve config data
	 * @param string|array $key
	 * @param string $sub
	 * @return mixed
	 */
	public static function get($key = NULL, $sub = NULL){

		if( is_null($key) ){
			return self::$config;
		}

		if( is_array($key) ){

			foreach( $key as $item ){
				$new[$item] = self::get($item, $sub);
			}

			return $new;
		}

		if( isset(self::$config[$key]) ){

			// Check with sub data
			if( !is_null($sub)
				AND isset(self::$config[$key][$sub]) ){
				return self::$config[$key][$sub];
			}

			if( is_null($sub) ){
				return self::$config[$key];
			}

		}

		return FALSE;
	}

	/**
	 * Instantiate class as singleton
	 * @param string $class
	 * @return object
	 */
	public static function singleton($class){

		if( !array_key_exists($class, self::$instances) ){
			self::$instances[$class] = new $class;
		}

		return self::$instances[$class];
	}

}