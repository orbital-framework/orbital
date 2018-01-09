<?php

abstract class I18n {

	/**
	 * Active language
	 * @var string
	 */
	private static $language = 'en-us';

	/**
	 * Active language scope
	 * @var string
	 */
	private  static $languageScope = 'default';

	/**
	 * Active language path
	 * @var string
	 */
	private static $languagePath = '';

	/**
	 * Translations
	 * @var array
	 */
	public static $texts = array();

	/**
	 * Load language translations
	 * @param string $language
	 * @return void
	 */
	public static function load($language = NULL){

		if( is_null($language) ){
			$language = self::getLanguage();
		}

		$folder = APP. 'I18n'. DS. $language;

		\App::importFolder($folder);
	}

	/**
	 * Retrieve active language
	 * @return string
	 */
	public static function getLanguage(){
		return self::$language;
	}

	/**
	 * Retrieve active language scope
	 * @return string
	 */
	public static function getLanguageScope(){
		return self::$languageScope;
	}

	/**
	 * Retrieve active language path
	 * @return string
	 */
	public static function getLanguagePath(){
		return self::$languagePath;
	}

	/**
	 * Set a new active language
	 * @param string $code
	 * @return void
	 */
	public static function setLanguage($code){
		self::$language = $code;
	}

	/**
	 * Set a new active language scope
	 * @param string $scope
	 * @return void
	 */
	public static function setLanguageScope($scope){
		self::$languageScope = $scope;
	}

	/**
	 * Set a new active language path
	 * @param string $path
	 * @return void
	 */
	public static function setLanguagePath($path){
		self::$languagePath = $path;
	}

	/**
	 * Add translations to language
	 * @param string $language
	 * @param string $scope
	 * @param array $texts
	 * @return void
	 */
	public static function add($language, $scope, $texts = array()){

		if( !isset(self::$texts[$language]) ){
			self::$texts[$language] = array();
		}

		if( !isset(self::$texts[$language][$scope]) ){
			self::$texts[$language][$scope] = array();
		}

		self::$texts[$language][$scope] = array_merge(
			self::$texts[$language][$scope],
			$texts
		);

	}

	/**
	 * Retrieve translations to language
	 * @param string $string
	 * @param array $placeholders
	 * @param string $scope
	 * @param string $language
	 * @return string
	 */
	public static function get($string, $placeholders = array(), $scope = NULL, $language = NULL){

		if( is_null($language) ){
			$language = self::getLanguage();
		}

		if( is_null($scope) ){
			$scope = self::getLanguageScope();
		}

		if( !isset(self::$texts[$language]) ){
			self::load($language);
		}

		if( !empty(self::$texts[$language])
			AND !empty(self::$texts[$language][$scope])
			AND isset(self::$texts[$language][$scope][$string]) ){
			$text = self::$texts[$language][$scope][$string];
		}else{
			$text = $string;
		}

		if( is_array($placeholders)
			AND $placeholders ){
			foreach( $placeholders as $key => $value ){
				$text = str_replace($key, $value, $text);
			}
		}

		return $text;
	}

}