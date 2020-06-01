<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Http\Client;


/**
 * Interface ClientInterface
 * @package sFire\Http
 */
interface ClientInterface {


	/**
	 * Add a custom header to the request
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function header(string $key, string $value);


	/**
	 * Add a cookie to the request
	 * @param string $name The name of the cookie
	 * @param string $value The value of the cookie
	 * @return $this
	 */
	public function cookie(string $name, string $value);


	/**
	 * Add a new key value param to the url or query
	 * @param string $key The name of the param
	 * @param string|array $value The value of the param
	 * @param bool $encode TRUE if key and/or value needs to be URL encoded
	 * @return $this
	 */
	public function param(string $key, $value, bool $encode = false);


	/**
	 * Add a new field with value to a post request
	 * @param string $key The name of the field
	 * @param string|array $value The value of the field
	 * @return $this
	 */
	public function field(string $key, $value);


	/**
	 * Attach file to a POST request
	 * @param string $file
	 * @param string $name Optional filename
	 * @param string $mime Optional MIME Type
	 * @return $this
	 */
	public function file(string $file, string $name = null, string $mime = null);


	/**
	 * Rest PUT method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function put(string $url, ?callable $closure = null);


	/**
	 * Rest DELETE method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function delete(string $url, ?callable $closure = null);

	
	/**
	 * Rest POST method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function post(string $url, ?callable $closure = null);

	
	/**
	 * Rest GET method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function get(string $url, ?callable $closure = null);


	/**
	 * Rest PATCH method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function patch(string $url, ?callable $closure = null);


	/**
	 * Rest HEAD method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function head(string $url, ?callable $closure = null);


	/**
	 * Rest OPTIONS method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function options(string $url, ?callable $closure = null);


	/**
	 * Rest TRACE method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function trace(string $url, ?callable $closure = null);


	/**
	 * Rest CONNECT method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return $this
	 */
	public function connect(string $url, ?callable $closure = null);


	/**
	 * Returns the body of the response
	 * @return null|string
	 */
	public function getBody();


	/**
	 * Returns the body in an array (if response is valid JSON)
	 * @return null|array
	 */
	public function getJson();


	/**
	 * Returns the headers of the response
	 * @return null|array
	 */
	public function getHeaders();


	/**
	 * Returns the status code of the response
	 * @return null|array
	 */
	public function getStatus();


	/**
	 * Returns information about the response
	 * @return null|array
	 */
	public function getInfo();


	/**
	 * Returns the response
	 * @return null|array
	 */
	public function getResponse();


	/**
	 * Returns all the cookies from the response
	 * @return null|array
	 */
	public function getCookies();


	/**
	 * Sends the request and sets all the response data
	 * @return $this
	 */
	public function send();
}