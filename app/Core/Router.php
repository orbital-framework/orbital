<?php

abstract class Router{

	/**
	 * Router URL
	 * @var string
	 */
	public static $url = NULL;

	/**
	 * Router Query
	 * @var string
	 */
	public static $query = NULL;

	/**
	 * Ajax flag
	 * @var boolean
	 */
	public static $isAjax = NULL;

	/**
	 * Request HTTP Method
	 * @var string
	 */
	public static $method = NULL;

	/**
	 * Active route
	 * @var array
	 */
	public static $route = array();

	/**
	 * Path for routers
	 * @var string
	 */
	public static $path = '/';

	/**
	 * "Routers" for errors - 404, 401...
	 * @var array
	 */
	public static $errors = array();

	/**
	 * Requests routers
	 * HTTP / WebDAV methods
	 * @var array
	 */
	private static $routers = array(
		# HEAD == GET
		'GET' => array(),
		'POST' => array(),
		'PUT' => array(),
		'DELETE' => array(),
		'CONNECT' => array(),
		'OPTIONS' => array(),
		'TRACE' => array(),
		'COPY' => array(),
		'LOCK' => array(),
		'MKCOL' => array(),
		'MOVE' => array(),
		'PROPFIND' => array(),
		'PROPPATCH' => array(),
		'UNLOCK' => array(),
		'REPORT' => array(),
		'MKACTIVITY' => array(),
		'CHECKOUT' => array(),
		'MERGE' => array()
	);

	/**
	 * Retrieve router active URL
	 * @return string
	 */
	public static function getActiveUrl(){

		if( self::$url == NULL ){
			self::processUrl();
		}

		return self::$url;
	}

	/**
	 * Retrieve router query
	 * @return string
	 */
	public static function getQuery(){

		if( self::$query == NULL ){
			self::processUrl();
		}

		return self::$query;
	}

	/**
	 * Retrive route query segment
	 * @param int $number
	 * @return string|FALSE
	 */
	public static function getSegment($number){

		$number = (int) $number - 1;
		$segment = explode('/', ltrim(self::getQuery(), '/') );

		return (isset($segment[$number])) ? $segment[$number] : FALSE;
	}

	/**
	 * Retrieve router HTTP Method
	 * @return string
	 */
	public static function getHttpMethod(){

		if( self::$method == NULL ){

			self::$method = ( isset($_SERVER['REQUEST_METHOD']) ) ?
								  $_SERVER['REQUEST_METHOD'] : 'GET';

			// Force GET when method is HEAD
			if( self::$method == 'HEAD' ){
				self::$method = 'GET';
			}

		}

		return self::$method;
	}

	/**
	 * Retrieve if router is ajax request
	 * @return booelan
	 */
	public static function getIsAjax(){

		if( self::$isAjax == NULL ){

			self::$isAjax = ( isset($_SERVER['HTTP_X_REQUESTED_WITH'])
				AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ? TRUE : FALSE;

		}

		return self::$isAjax;
	}

	/**
	 * Set path prefix to routers
	 * @param string $path
	 * @return void
	 */
	public static function setPath($path){

		$path = '/'. trim($path, '/'). '/';
		$path = str_replace('//', '/', $path);

		self::$path = $path;
	}

	/**
	 * Retrieve path prefix to routers
	 * @return string
	 */
	public static function getPath(){
		return self::$path;
	}

	/**
	 * Set active route
	 * @param array $route
	 * @return void
	 */
	public static function setActiveRoute($route){
		self::$route = $route;
	}

	/**
	 * Retrieve active route
	 * @return array
	 */
	public static function getActiveRoute(){

		if( !self::$route ){

			$route = self::getRoute(
				self::getQuery(),
				self::getHttpMethod()
			);

			self::setActiveRoute($route);

		}

		return self::$route;
	}

	/**
	 * Retrieve route from $uri and $method
	 * @param string $uri
	 * @param string $method
	 * @return array
	 */
	public static function getRoute($uri, $method){

		$route = array();

		if( !array_key_exists($method, self::$routers) ){
			return $route;
		}

		$routers = self::$routers[ $method ];

		foreach( $routers as $router ){

			$pattern = '/^'.
				str_replace(
					array('(:any)', '(:string)', '(:number)', '/'),
					array('([-0-9a-z]+)', '([a-z]+)', '([0-9]+)', '\/'),
				$router['rule']).
				'$/i';

			if( preg_match($pattern, $uri) OR $router['rule'] == $uri ){

				$rule = $router['rule'];
				$callback = $router['callback'];
				$parameters = FALSE;
				$options = array();

				if( is_array($router['parameters']) ){
					$parameters = array();

					foreach( $router['parameters'] as $parameter ){

						// Segments
						if( strpos($parameter, '$') === 0
							AND is_numeric(str_replace('$', '', $parameter)) ){
							$parameter = str_replace('$', '', $parameter);
							$parameter = self::getSegment($parameter);
						}

						$parameters[] = $parameter;
					}

				}

				// Check for not authorized parameters
				if( $parameters == FALSE
					AND self::getSegment( count(explode('/', $rule)) + 1) ){
					continue;
				}

				if( !$parameters ){
					$parameters = array();
				}

				if( is_array($router['options']) ){
					$options = $router['options'];
				}

				$route = array(
					'rule' => $rule,
					'callback' => $callback,
					'parameters' => $parameters,
					'options' => $options
				);

				break;
			}

		}

		return $route;
	}

	/**
	 * Process request and run callback
	 * @return void
	 */
	public static function runRequest(){

		$route = self::getActiveRoute();

		if( !$route ){
			return self::runError(404);
		}

		$options = $route['options'];

		if( $options
			AND isset($options['contentType'])
			AND !is_null($options['contentType']) ){
			Header::contentType($options['contentType']);
		}

		if( $options
			AND isset($options['status'])
			AND !is_null($options['status']) ){
			Header::status($options['status']);
		}

		Header::send();

		return self::runCallback(
			$route['callback'],
			$route['parameters']
		);
	}

	/**
	 * Process request URL
	 * @return void
	 */
	private static function processUrl(){

		$url = str_replace('/index.php', '', $_SERVER['PHP_SELF']);

		if( isset($_SERVER['REQUEST_URI']) ){
			$url = str_replace($url, '', $_SERVER['REQUEST_URI']);
		}

		$query = explode('?', $url);
		$query = $query[0];
		$query = ($query !== '/') ? rtrim($query,'/') : $query;

		self::$url = $url;
		self::$query = strtolower($query);

	}

	/**
	 * Set routers to APP
	 * @param string $httpMethod
	 * @param string $rule
	 * @param string $callback
	 * @param array $parameters
	 * @param array $options
	 * @return void
	 */
	public static function set(
		$httpMethod,
		$rule,
		$callback,
		$parameters = array(),
		$options = array()
		){

		if( is_array($httpMethod) ){

			foreach( $httpMethod as $new ){
				self::set(
					$new,
					$rule,
					$callback,
					$parameters,
					$options
				);
			}

			return;
		}

		$path = self::getPath(). trim($rule, '/');

		$router = array(
			'rule' => $path,
			'callback' => $callback,
			'parameters' => $parameters,
			'options' => $options
		);

		self::$routers[ $httpMethod ][] = $router;
	}

	/**
	 * Set error callback when router goes wrong
	 * @param string $number
	 * @param string $callback
	 * @return void
	 */
	public static function setError($number, $callback){

		self::$errors[$number] = array(
			'callback' => $callback
		);

	}

	// CALLBACK METHODS

	/**
	 * Run callback
	 * @param string $callback
	 * @param array $parameters
	 * @return object
	 */
	public static function runCallback($callback, $parameters = array()){

		$callback = explode('@', $callback);
		$controller = $callback[0];
		$method = $callback[1];

		if( !class_exists($controller) ){
			return self::runError(500);
		}

		$controller = App::singleton($controller);

		// If method not exists or is not public
		if( !is_callable(array($controller, $method)) ){
			return self::runError(500);
		}

		return $controller->$method(...$parameters);
	}

	/**
	 * Force error on request
	 * @param int $number
	 * @return void
	 */
	public static function runError($number = 404){

		if( !isset(self::$errors[$number]) ){
			die('Router error '. $number);
		}

		$callback = self::$errors[ $number ]['callback'];

		self::setActiveRoute(array(
			'rule' => $number,
			'callback' => $callback,
			'parameters' => array(),
			'options' => array('status' => $number)
		));

		self::runRequest();

	}

	// URL METHODS

	/**
	 * Create valid URI
	 * @param string $text
	 * @return string
	 */
	public static function createUri($text){

		$text = strtolower($text);

		$accents = array(
			'á', 'à', 'â', 'ã',
			'é', 'è', 'ê',
			'í', 'ì', 'î',
			'ó', 'ò', 'ô', 'õ',
			'ú', 'ù', 'û', 'ç'
		);

		$nonAccents = array(
			'a', 'a', 'a', 'a',
			'e', 'e', 'e',
			'i', 'i', 'i',
			'o', 'o', 'o', 'o',
			'u', 'u', 'u', 'c'
		);

		$text = str_replace($accents, $nonAccents, $text);
		$text = preg_replace("/[^a-z0-9_\s-]/", "", $text);
		$text = preg_replace("/[\s-]+/", " ", $text);
		$text = preg_replace("/[\s_]/", "-", $text);
		$text = trim($text, '-');

		return $text;
	}

	/**
	 * Create and format URL
	 * @param string $url
	 * @param string $path
	 * @param string $query
	 * @return string
	 */
	public static function createUrl($url, $path = '', $query = NULL){

		$url = trim($url, '/');

		if( !empty($path) ){
			$url.= '/'. $path;
		}

		if( !empty($query) ){
			$url .= '/?'. str_replace('?', '', $query);
		}

		$url = preg_replace('/((?<!:)\/{2,4}\/?)/', '/', $url);

		return $url;
	}

	/**
	 * Retrieve URL
	 * @param string $path
	 * @param string $query
	 * @param boolean $ignoreLanguage
	 * @return string
	 */
	public static function getUrl($path = '', $query = NULL, $ignoreLanguage = TRUE){

		if( $path == '$this' ){
			$path = ($query == TRUE) ? self::$url : self::$query;
			$query = NULL;
			$ignoreLanguage = TRUE;
		}

		$url = App::get('url');

		if( !$ignoreLanguage AND I18n::getLanguagePath() ){
			$url .= '/'. trim(I18n::getLanguagePath(), '/');
		}

		return self::createUrl($url, $path, $query);
	}

	/**
	 * Print URL
	 * @param string $path
	 * @param string $query
	 * @return void
	 */
	public static function url($path = '', $query = NULL){
		echo self::getUrl($path, $query);
	}

	/**
	 * Retrieve URL for language
	 * @param string $path
	 * @param string $query
	 * @return string
	 */
	public static function getLanguageUrl($path = '', $query = NULL){
		return self::getUrl($path, $query, FALSE);
	}

	/**
	 * Print URL for language
	 * @param string $path
	 * @param string $query
	 * @return void
	 */
	public static function languageUrl($path = '', $query = NULL){
		echo self::getLanguageUrl($path, $query);
	}

}