<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Http;


/**
 * Class Cookie
 * @package sFire\Http
 */
class Cookie {


    /**
     * Contains instance of Cookie
     * @var null|Cookie
     */
    private static ?self $instance = null;


	/**
	 * Contains all the arbitrary data
	 * @var array
	 */
	protected static array $data = [];


	/**
	 * Constructor
	 */
	public function __construct() {
		static::$data = &$_COOKIE;
	}


    /**
     * Returns an instance of self
     * @return self
     */
    public static function getInstance(): self {

        if(null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }


	/**
	 * Sets a new cookie and sends it to the browser
	 * @param string $name The name of the cookie
	 * @param string $value The content of the cookie
	 * @param int $seconds Expiration in seconds from current timestamp
	 * @param string $path The path on the server in which the cookie will be available on
	 * @param string $domain The (sub)domain that the cookie is available to
	 * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client
	 * @param bool $httpOnly When TRUE the cookie will be made accessible only through the HTTP protocol
	 * @return bool
	 */
	public static function add(string $name, string $value = null, int $seconds = 0, string $path = null, string $domain = null, bool $secure = null, bool $httpOnly = null): bool {

		$cookie = session_get_cookie_params();
		return setcookie($name, $value, time() + $seconds, ($path ?? $cookie['path']), ($domain ?? $cookie['domain']), ($secure ?? $cookie['secure']), ($httpOnly ?? $cookie['httponly']));
	}


	/**
	 * Retrieve the value of the cookie and delete it after
	 * @param string $name The name of the cookie
	 * @param mixed $default A default value if the cookie could not be found
	 * @return mixed
	 */
	public static function pull(string $name, $default = null) {

		if(true === isset(static::$data[$name])) {
			
			$default = static :: get($name);
			static :: add($name, null, -99999999);
		}

		return $default;
	}


	/**
	 * Remove data based on name 
	 * @param string $name The name of the cookie
	 * @return void
	 */
	public static function remove(string $name = null): void {

		if($name && isset(static::$data[$name])) {
			static :: add($name, null, -99999999);
		}
	}


	/**
	 * Deletes all the cookies
	 * @return void
	 */
	public static function flush(): void {
		
		foreach(static::$data as $key => $value) {
			static :: add($key, null, -99999999);
		}
	}


	/**
	 * Retrieve cookie value based on cookie name
	 * @param string $name The name of the cookie
	 * @param mixed $default A default value if the cookie could not be found
	 * @return mixed
	 */
	public static function get(string $name, $default = null) {

		if(true === isset(static::$data[$name])) {
			return static::$data[$name];
		}

		return $default;
	}


	/**
	 * Get all cookies
	 * @return array
	 */
	public static function getAll(): array {
		return static::$data;
	}
}