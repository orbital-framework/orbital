<?php

abstract class Observer{

	/**
	 * Observers
	 * @var array
	 */
	public static $observers = array();

	/**
	 * Add watches to event
	 * @param string $event
	 * @param string $class
	 * @param string $method
	 * @return void
	 */
	public static function on($event, $class, $method){

		if( !isset(self::$observers[ $event ]) ){
			self::$observers[ $event ] = array();
		}

		self::$observers[ $event ][] = array($class, $method);
	}

	/**
	 * Remove all watches on event
	 * @param string $event
	 * @return void
	 */
	public static function off($event){
		unset(self::$observers[ $event ]);
	}

	/**
	 * Fire event and process all watches
	 * @param string $event
	 * @param array $parameters
	 * @param boolean $overwrite
	 * @return mixed
	 */
	public static function fire($event, $parameters = array(), $overwrite = FALSE){

		if( !isset(self::$observers[ $event ]) ){
			return $parameters;
		}

		$observers = self::$observers[ $event ];

		foreach( $observers as $observer ){
			$ret = call_user_func_array($observer, array($parameters));
			if( $overwrite ){
				$parameters = $ret;
			}
		}

		return $parameters;
	}

}