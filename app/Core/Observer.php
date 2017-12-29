<?php

abstract class Observer {

	/**
	 * Observers
	 * @var array
	 */
	public static $observers = array();

	/**
	 * Add watches to event
	 * @param string $event
	 * @param string $callback
	 * @return void
	 */
	public static function on($event, $callback){

		if( !isset(self::$observers[ $event ]) ){
			self::$observers[ $event ] = array();
		}

		self::$observers[ $event ][] = $callback;
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
			$result = \App::runMethod($observer, $parameters);
			if( $overwrite ){
				$parameters = $result;
			}
		}

		return $parameters;
	}

}