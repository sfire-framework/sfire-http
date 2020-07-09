<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Http\Client\Adapter;

use sFire\Http\Exception\BadMethodCallException;
use sFire\Http\Client\ClientAbstract;
use sFire\Http\Exception\InvalidArgumentException;


/**
 * Class Curl
 * @package sFire\Http
 */
class Curl extends ClientAbstract {


    public const MAXREDIRS 			 	= 'CURLOPT_MAXREDIRS';
    public const AUTH_BASIC 			= 'CURLAUTH_BASIC';
    public const AUTH_DIGEST 			= 'CURLAUTH_DIGEST';
    public const AUTH_GSSNEGOTIATE 	 	= 'CURLAUTH_GSSNEGOTIATE';
    public const AUTH_NTLM 			 	= 'CURLAUTH_NTLM';
    public const AUTH_ANY 				= 'CURLAUTH_ANY';
    public const AUTH_ANYSAFE 			= 'CURLAUTH_ANYSAFE';
    public const HTTP_NONE				= 'CURL_HTTP_VERSION_NONE';
    public const HTTP_1_0				= 'CURL_HTTP_VERSION_1_0';
    public const HTTP_1_1				= 'CURL_HTTP_VERSION_1_1';
    public const HTTP_2_0				= 'CURL_HTTP_VERSION_2_0';
    public const HTTP_2TLS				= 'CURL_HTTP_VERSION_2TLS';
    public const HTTP_2_PRIOR_KNOWLEDGE = 'CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE';


    /**
     * Contains all the CURL options
     * @var array
     */
    private array $options = [];


    /**
     * Constructor
     * Checks if curl is available
     * @throws BadMethodCallException
     */
    public function __construct() {

        if(false === function_exists('curl_init')) {
            throw new BadMethodCallException(sprintf('Function "curl_init" should be loaded to use %s', __CLASS__));
        }

        parent :: __construct();
    }


    /**
     * Set CURL options like CURLOPT_SSL_VERIFYPEER
     * @param mixed $option A CURL option. See https://php.net/curl-setopt
     * @param mixed $value The value of the option
     * @return void
     */
    public function setOption($option, $value): void {
        $this -> options[$option] = $value;
    }


    /**
     * Adds a HTTP authentication method
     * @param string $username The username
     * @param string $password The password
     * @param string $type The authentication type
     * @return Curl
     * @throws InvalidArgumentException
     */
    public function authenticate(string $username, string $password, string $type = self::AUTH_ANY): Curl {

        if(false === defined('SELF::' . $type)) {
            throw new InvalidArgumentException(sprintf('Argument 3 passed to %s() is not a valid authentication type', __METHOD__));
        }

        $this -> options[CURLOPT_USERPWD]  = sprintf('%s:%s', $username, $password);
        $this -> options[CURLOPT_HTTPAUTH] = constant($type);

        return $this;
    }


    /**
     * Set a user agent to the request
     * @param string $useragent The useragent string
     * @return Curl
     */
    public function userAgent(string $useragent): Curl {

        $this -> options[CURLOPT_USERAGENT] = $useragent;
        return $this;
    }


    /**
     * Set the connection and response timeout in seconds for the request
     * @param int $connectionTimeout The timeout in seconds for the connection
     * @param int $responseTimeout The timeout in seconds  for the response
     * @return Curl
     */
    public function timeout(int $connectionTimeout = 30, int $responseTimeout = 30): Curl  {

        $this -> options[CURLOPT_CONNECTTIMEOUT] = $connectionTimeout;
        $this -> options[CURLOPT_TIMEOUT] 		 = $responseTimeout;

        return $this;
    }


    /**
     * Set the port
     * @param int $port The port number to use
     * @return Curl
     * @throws InvalidArgumentException
     */
    public function port(int $port): Curl  {

        if($port < 0 || $port > 65535) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be between 0 and 65535, "%s" given', __METHOD__, $port));
        }

        $this -> options[CURLOPT_PORT] = intval($port);

        return $this;
    }


    /**
     * Set the referer header
     * @param string $referer The referrer
     * @return Curl
     */
    public function referer(string $referer): Curl {

        $this -> options[CURLOPT_REFERER] = $referer;
        return $this;
    }


    /**
     * Will follow as many "Location: " headers until the amount given
     * @param int $amount The amount of following header redirects
     * @return Curl
     */
    public function follow(int $amount): Curl {

        $this -> options[CURLOPT_FOLLOWLOCATION] = true;
        $this -> options[CURLOPT_MAXREDIRS] 	 = $amount;

        return $this;
    }


    /**
     * Set the HTTP version protocol
     * @param string $version The version of the HTTP protocol
     * @return Curl
     * @throws InvalidArgumentException
     */
    public function httpVersion(string $version): Curl {

        if(false === defined($version)) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() is not a valid HTTP protocol version', __METHOD__));
        }

        $this -> options[CURLOPT_HTTP_VERSION] = constant($version);

        return $this;
    }


    /**
     * Sends the request and sets all the response data
     * @return Curl
     */
    public function send(): Curl {

        //Set default status
        $this -> response -> setStatus([

            'code' 		=> 0,
            'protocol' 	=> '',
            'status' 	=> '',
            'text' 		=> ''
        ]);

        $this -> options[CURLOPT_CUSTOMREQUEST] = strtoupper($this -> method);

        $curl = curl_init();

        curl_setopt_array($curl, $this -> createOptions());

        $response = curl_exec($curl);

        if(false !== $response) {

            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers 	 = substr($response, 0, $header_size);
            $headers 	 = $this -> parseHeaders($headers);
            $body 		 = substr($response, $header_size);
            $json 		 = @json_decode($body, true);

            $this -> response -> setHeaders($headers['headers']);
            $this -> response -> setBody($body);
            $this -> response -> setStatus($headers['status']);
            $this -> response -> setCookies($this -> parseCookies($response));
            $this -> response -> setResponse($response);

            if(true === (json_last_error() == JSON_ERROR_NONE)) {
                $this -> response -> setJson($json);
            }
        }

        $this -> response -> setInfo(curl_getinfo($curl));

        curl_close($curl);

        return $this;
    }


    /**
     * Generates and returns all the options
     * @return array
     */
    private function createOptions(): array {

        $default = [

            CURLOPT_RETURNTRANSFER 	=> true,
            CURLOPT_HEADER 			=> true,
            CURLOPT_TIMEOUT			=> 30,
            CURLOPT_URL 			=> $this -> formatUrl()
        ];

        //Add cookies
        if(count($this -> cookies) > 0) {
            $default[CURLOPT_COOKIE] = $this -> formatCookies();
        }

        //Add headers
        if(count($this -> headers) > 0) {
            $default[CURLOPT_HTTPHEADER] = $this -> headers;
        }

        if('get' !== $this -> method) {
            $this -> options[CURLOPT_POSTFIELDS] = $this -> formatFields();
        }

        return $default + $this -> options;
    }


    /**
     * Formats all the POST field and files
     * @return array
     */
    private function formatFields(): array {

        $params = [];

        //Files
        foreach($this -> files as $file) {

            if(true === function_exists('curl_file_create')) {
                $value = curl_file_create($file -> file -> entity() -> getBasepath());
            }
            else {
                $value = '@' . realpath($file -> file -> entity() -> getBasepath());
            }

            $params[$file -> name] = $value;
        }

        return $params + $this -> fields;
    }
}