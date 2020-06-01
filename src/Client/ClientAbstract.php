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

use sFire\Http\Exception\InvalidArgumentException;
use sFire\Http\Exception\RuntimeException;
use sFire\Http\UrlParser;
use sFire\FileControl\File;


/**
 * Class ClientAbstract
 * @package sFire\Http
 */
abstract class ClientAbstract implements ClientInterface {


	/**
	 * Holds an instance of Response
	 * Response
	 */
	protected ?Response $response;


	/**
	 * Contains an instance of UrlParser
	 * UrlParser
	 */
	protected ?UrlParser $url;


	/**
	 * Contains the HTTP method like GET or POST
	 * string $method
	 */
	protected ?string $method;


	/**
	 * Contains all the headers that needs to be send
	 * array
	 */
	protected array $headers = [];


	/**
	 * Contains extra query parameters for in the URL that needs to be fetched
	 * array
	 */
	protected array $params = [];


	/**
	 * Contains all the POST fields with their values that needs to be send
	 * array
	 */
	protected array $fields = [];


	/**
	 * Contains all the files that needs to be send
	 * File[]
	 */
	protected array $files = [];


	/**
	 * Contains all the cookies and their values that needs to be send
	 * array
	 */
	protected array $cookies = [];


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> response = new Response();
	}


	/**
	 * Rest PUT method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function put(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'put');
	}


	/**
	 * Rest DELETE method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function delete(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'delete');
	}

	
	/**
	 * Rest POST method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function post(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'post');
	}

	
	/**
	 * Rest GET method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function get(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'get');
	}


	/**
	 * Rest PATCH method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function patch(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'patch');
	}


	/**
	 * Rest HEAD method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function head(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'head');
	}


	/**
	 * Rest OPTIONS method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function options(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'options');
	}


	/**
	 * Rest TRACE method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function trace(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'trace');
	}


	/**
	 * Rest CONNECT method 
	 * @param string $url The url to connect to
	 * @param callable $closure A function which can be used to set more options before sending the request
	 * @return self
	 */
	public function connect(string $url, ?callable $closure = null): self {
		return $this -> method($url, $closure, 'connect');
	}


	/**
	 * Returns the body of the response
	 * @return null|string
	 */
	public function getBody(): ?string {

		if(null !== $this -> response) {
			return $this -> response -> getBody();
		}

		return null;
	}


	/**
	 * Returns the body in an array (if response is valid JSON)
	 * @return null|array
	 */
	public function getJson(): ?array {

		if(null !== $this -> response) {
			return $this -> response -> getJson();
		}

        return null;
	}


	/**
	 * Returns the headers of the response
	 * @return null|array
	 */
	public function getHeaders(): ?array {

		if(null !== $this -> response) {
			return $this -> response -> getHeaders();
		}

        return null;
	}


	/**
	 * Returns the status code of the response
	 * @return null|array
	 */
	public function getStatus(): ?array {

		if(null !== $this -> response) {
			return $this -> response -> getStatus();
		}

        return null;
	}


	/**
	 * Returns information about the response
	 * @return null|array
	 */
	public function getInfo(): ?array {

		if(null !== $this -> response) {
			return $this -> response -> getInfo();
		}

        return null;
	}


	/**
	 * Returns the response
	 * @return null|string
	 */
	public function getResponse(): ?string {

		if(null !== $this -> response) {
			return $this -> response -> getResponse();
		}

        return null;
	}


	/**
	 * Returns all the cookies from the response
	 * @return null|array
	 */
	public function getCookies(): ?array {

		if(null !== $this -> response) {
			return $this -> response -> getCookies();
		}

        return null;
	}


	/**
	 * Add a custom header to the request
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function header(string $key, string $value) {

		$this -> headers[$key] = sprintf('%s:%s', $key, $value);
		return $this;
	}


	/**
	 * Add a cookie to the request
	 * @param string $name The name of the cookie
	 * @param string $value The value of the cookie
	 * @return $this
	 */
	public function cookie(string $name, string $value) {

		$this -> cookies[] = sprintf('%s=%s', $name, $value);
		return $this;
	}


    /**
     * Add a new key value param to the url or query
     * @param string $key The name of the param
     * @param string|array $value The value of the param
     * @param bool $encode TRUE if key and/or value needs to be URL encoded
     * @return $this
     * @throws InvalidArgumentException
     */
	public function param(string $key, $value, bool $encode = true) {

        if(false === is_string($value) && false === is_array($value)) {
            throw new InvalidArgumentException(sprintf('Argument 2 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($value)));
        }

		$this -> params[] = (object) ['key' => $key, 'value' => $value, 'encode' => $encode];

		return $this;
	}


    /**
     * Add a new field with value to a post request
     * @param string $key The name of the field
     * @param string|array $value The value of the field
     * @return $this
     * @throws InvalidArgumentException
     */
	public function field(string $key, $value) {

		if(false === is_string($value) && false === is_array($value)) {
            throw new InvalidArgumentException(sprintf('Argument 2 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($value)));
        }

		$this -> fields[$key] = $value;

		return $this;
	}


    /**
     * Attach file to a POST request
     * @param string $file
     * @param string $name Optional filename
     * @param string $mime Optional MIME Type
     * @return $this
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
	public function file(string $file, string $name = null, string $mime = null) {

		if('post' !== $this -> method) {
			throw new InvalidArgumentException('File uploads are only supported with POST request method');
		}

		$file = new File($file);

		if(false === $file -> isReadable()) {
			throw new RuntimeException(sprintf('File %s passed to %s() is not readable', $file -> getPath(), __METHOD__));
		}

		if(null === $name) {
			$name = $file -> getBasename();
		}

		if(null === $mime) {
			$mime = $file -> getMimeType();
		}

		$this -> files[] = (object) ['file' => $file, 'name' => $name, 'mime' => $mime];

		return $this;
	}


	/**
	 * Parses headers and forms it into an array
	 * @param string $headers The headers as a string
	 * @return array
	 */
	protected function parseHeaders(string $headers): array {

		$parsed = [];
		$status = [];

		foreach(explode("\n", $headers) as $header) {
			
			if(preg_match('/(:)/is', $header)) {
				list($key, $value) = preg_split('/(:)/is', $header, 2);
			}
			else {

			    $key = $header;
				$value = '';
			}

			if(trim($key) !== '') {

				if(preg_match('/(https?\/[0-9]\.[0-9])/i', $key, $http)) {

					$status['code'] = null;

					if(preg_match('/([0-9]+)/', ltrim($key, $http[0]), $code)) {
						$status['code'] = $code[0];
					}

					$status['protocol'] = $http[0];
					$status['status'] 	= trim($key);
					$status['text'] 	= trim(ltrim(trim(ltrim($key, $status['protocol'])), $status['code']));
					
				}
				else {
				    $parsed[strtolower($key)] = trim($value);
				}
			}
		}

		return ['headers' => $parsed, 'status' => $status];
	}


	/**
	 * Parses the cookies from the headers and forms it into an array
	 * @param string $cookies
	 * @return array
	 */
	protected function parseCookies(string $cookies): array {  

	    $jar 	 = [];
		$headers = explode("\n", trim($cookies));

		foreach($headers as $header) {

			if(preg_match('/^set-cookie: /i', $header)) {
				
				$cookie = [];

				//Match name and value
				preg_match('/^set-cookie: ([^=]+)=([^;]+)/i', $header, $match);

				if(count($match) === 3) {
					
					$cookie['name']  = $match[1];
					$cookie['value'] = $match[2];
				
					//Match expires
					$cookie['expires'] = null;

					preg_match('/; expires=([^;]+)/i', $header, $match);

					if(count($match) === 2) {
						$cookie['expires'] = strtotime(urldecode(trim($match[1])));
					}

					//Match domain
					$cookie['domain'] = null;

					preg_match('/; domain=([^;]+)/i', $header, $match);

					if(count($match) === 2) {
						$cookie['domain'] = urldecode(trim($match[1]));
					}

					//Match secure
					$cookie['secure'] = false;

					preg_match('/; secure/i', $header, $match);

					if(count($match) === 1) {
						$cookie['secure'] = true;
					}

					//Match httponly
					$cookie['httponly'] = false;

					preg_match('/; httponly/i', $header, $match);

					if(count($match) === 1) {
						$cookie['httponly'] = true;
					}

					//Add cookie to the jar
					if(count($cookie) > 0) {
						$jar[] = $cookie;
					}
				}
			}
		}

		return $jar;
	}


	/**
	 * Adds new params to existing params to format the url
	 * @return string
	 */
	protected function formatUrl(): string {

		$jar 	= [];
		$query 	= $this -> url -> getQuery();

		if(null !== $query) {
			$jar[] = $query;
		}
		
		$query 	= '';

		if(count($this -> params) > 0 || count($jar) > 0) {
				
			foreach($this -> params as  $param) {

				$key  	= $param -> encode ? urlencode($param -> key) : $param -> key;
				$value 	= $param -> encode ? urlencode($param -> value) : $param -> value;

				$jar[] = sprintf('%s=%s', $key, $value);
			}

			$query = '?' . implode('&', $jar);
		}

		return $this -> url -> generate(UrlParser::PATH) . $query;
	}


	/**
	 * Formats all the cookie parameters
	 * @return string
	 */
	protected function formatCookies(): string {
		return implode(';', $this -> cookies);
	}


	/**
	 * Sets the method and url and executes a user closure function if given
	 * @param string $url The URL to be fetched
	 * @param callable $closure
	 * @param string $method A HTTP method
	 * @return self
	 */
	protected function method(string $url, ?callable $closure, string $method): self {
			
		$this -> method = $method;
		$this -> url 	= new UrlParser($url);

		if(null !== $closure) {
			call_user_func($closure, $this);
		}

		$this -> send();

		return $this;
	}
}