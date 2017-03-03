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
	 * Retrieve active route
	 * @return array
	 */
	public static function getActiveRoute(){

		if( !self::$route ){
			self::$route = self::getRoute(
				self::getQuery(),
				self::getHttpMethod()
			);
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
				$controller = $router['controller'];
				$method = $router['method'];
				$parameters = FALSE;

				if( is_array($router['parameters']) ){
					$parameters = array();

					foreach( $router['parameters'] as $parameter ){

						// Segments
						if( strpos($parameter, '$') === 0
							AND is_numeric(str_replace('$', '', $parameter)) ){
							$parameter = self::getSegment(str_replace('$', '', $parameter));
						}

						$parameters[] = $parameter;
					}

				}

				// Check for not authorized parameters
				if( $parameters == FALSE
					AND self::getSegment((int) count(explode('/', $rule)) + (int) 1) ){
					continue;
				}

				$route = array(
					'route' => $rule,
					'controller' => $controller,
					'method' => $method,
					'parameters' => $parameters,
					'contentType' => $router['contentType']
				);

				break;
			}

		}

		return $route;
	}

	/**
	 * Process request and run controller
	 * @return void
	 */
	public static function runRequest(){

		$route = self::getActiveRoute();

		if( !$route ){
			return self::runError(404);
		}

		if( !is_null($route['contentType']) ){
			Header::contentType($route['contentType']);
			Header::send();
		}

		return self::runController(
			$route['controller'],
			$route['method'],
			$route['parameters'],
			TRUE
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
	 * @param string $controller
	 * @param string $method
	 * @param array $parameters
	 * @param string $contentType
	 * @return void
	 */
	public static function set($httpMethod, $rule, $controller, $method, $parameters = array(), $contentType = NULL){

		if( is_array($httpMethod) ){

			foreach( $httpMethod as $new ){
				self::set(
					$new,
					$rule,
					$controller,
					$method,
					$parameters,
					$contentType
				);
			}

			return;
		}

		$router = array(
			'rule' => ($rule !== '/') ? rtrim($rule,'/') : $rule,
			'controller' => $controller,
			'method' => $method,
			'parameters' => $parameters,
			'contentType' => $contentType
		);

		self::$routers[ $httpMethod ][] = $router;
	}

	/**
	 * Set error controllers when router goes wrong
	 * @param string $number
	 * @param string $controller
	 * @param string $method
	 * @return void
	 */
	public static function setError($number, $controller, $method){

		self::$errors[$number] = array(
			'controller' => $controller,
			'method' => $method
		);

	}

	// CONTROLLER METHODS

	/**
	 * Instantiate a controller with singleton
	 * @param string $controller
	 * @param boolean|string $method
	 * @param boolean|array $parameters
	 * @param boolean $run
	 * @return object
	 */
	public static function runController($controller, $method = FALSE, $parameters = FALSE, $run = FALSE){

		if( !class_exists($controller) ){
			return self::runError();
		}

		$controller = App::singleton($controller);

		if( $run ){

			// If method not exists or is not public
			if( !is_callable(array($controller, $method)) ){
				return self::runError();
			}

			if( is_array($parameters) ){
				call_user_func_array(array($controller, $method), $parameters);
			}else{
				$controller->$method();
			}

		}

		return $controller;
	}

	/**
	 * Force error on request
	 * @param int $number
	 * @return void
	 */
	public static function runError($number = 404){

		if( !isset(Router::$errors[$number]) ){
			die('Error '. $number);
		}

		$controller = Router::$errors[$number]['controller'];
		$method = Router::$errors[$number]['method'];

		Header::status($number);
		Header::send();

		$controller = App::singleton($controller);
		$controller->$method();

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
			'ú', 'ù', 'û'
		);

		$nonAccents = array(
			'a', 'a', 'a', 'a',
			'e', 'e', 'e',
			'i', 'i', 'i',
			'o', 'o', 'o', 'o',
			'u', 'u', 'u'
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
			$path = ($query == TRUE) ? Router::$url : Router::$query;
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