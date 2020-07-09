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

use sFire\Http\Exception\BadFunctionCallException;
use sFire\Http\Exception\InvalidArgumentException;
use sFire\Http\Client\ClientAbstract;


/**
 * Class FileGetContents
 * @package sFire\Http
 */
class FileGetContents extends ClientAbstract {


    public const AUTH_BASIC = 'AUTH_BASIC';


    /**
     * Contains all the options for the request (i.e. timeout, useragent, follow_location, etc.)
     * array
     */
    private array $options = [];


    /**
     * Contains the RAW HTTP body for the request
     * string
     */
    private ?string $body;


    /**
     * Constructor
     * @throws BadFunctionCallException
     */
    public function __construct() {

        if('1' !== ini_get('allow_url_fopen')) {
            throw new BadFunctionCallException('Using file_get_contents is not supported.');
        }

        parent :: __construct();
    }


    /**
     * Adds a HTTP authentication method
     * @param string $username The username
     * @param string $password The password
     * @param string $type The authentication type
     * @return self
     * @throws InvalidArgumentException
     */
    public function authenticate(string $username, string $password, string $type = self::AUTH_BASIC): self {

        if(false === defined('SELF::' . $type)) {
            throw new InvalidArgumentException(sprintf('Argument 3 passed to %s() is not a valid authentication type', __METHOD__));
        }

        $this -> header('Authorization', 'Basic ' . base64_encode(sprintf('%s:%s', $username, $password)));

        return $this;
    }


    /**
     *
     * @param string $body RAW HTTP body as a string
     * @return self
     */
    public function setRawBody(string $body): self {

        $this -> body = $body;
        return $this;
    }


    /**
     * Set a user agent to the request
     * @param string $useragent The user agent string
     * @return self
     */
    public function userAgent(string $useragent): self {

        $this -> options['user_agent'] = $useragent;
        return $this;
    }


    /**
     * Set the connection and response timeout in seconds for the request
     * @param int $connection The timeout for the connection
     * @return self
     */
    public function timeout(int $connection = 30): self  {

        $this -> options['timeout'] = $connection;
        return $this;
    }


    /**
     * Set the referer header
     * @param string $referer The referrer
     * @return self
     */
    public function referer(string $referer): self {

        $this -> header('Referer', $referer);
        return $this;
    }


    /**
     * Will follow as many "Location: " headers until the amount given
     * @param int $amount The amount of following header redirects
     * @return self
     */
    public function follow(int $amount): self {

        $this -> options['follow_location'] = $amount;
        $this -> options['max_redirects'] 	= $amount;

        return $this;
    }


    /**
     * Set the HTTP version protocol
     * @param float $version The version of the HTTP protocol (1.0, 1.1, 2.0, etc.)
     * @return self
     */
    public function httpVersion(float $version): self {

        $this -> options['protocol_version'] = $version;
        return $this;
    }


    /**
     * Set the port
     * @param int $port The port number to use
     * @return self
     * @throws InvalidArgumentException
     */
    public function port(int $port): self  {

        if($port < 0 || $port > 65535) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be between 0 and 65535, "%s" given', __METHOD__, $port));
        }

        $this -> url -> setPort($port);

        return $this;
    }


    /**
     * Sends the request and sets all the response data
     * @return self
     */
    public function send(): self {

        //Set default status
        $this -> response -> setStatus([

            'code' 		=> 0,
            'protocol' 	=> '',
            'status' 	=> '',
            'text' 		=> ''
        ]);

        //Add cookies
        if(count($this -> cookies) > 0) {
            $this -> header('Cookie', implode(';', $this -> cookies));
        }

        //Set the context options
        $options = [

            'method'  => strtoupper($this -> method),
            'header'  => $this -> headers,
            'content' => $this -> generateContent()
        ];

        $options = array_merge($options, $this -> options);

        //Create context
        $context = stream_context_create(['http' => $options]);

        //Send request
        $url 	  = $this -> formatUrl();
        $start 	  = microtime(true);
        $response = @file_get_contents($url, false, $context);
        $end 	  = microtime(true);
        $header   = implode($http_response_header, "\n");
        $headers  = $this -> parseHeaders($header);

        if(false !== $response) {

            $body 	 = $response;
            $json 	 = @json_decode($body, true);

            $this -> response -> setHeaders($headers['headers']);
            $this -> response -> setBody($body);
            $this -> response -> setStatus($headers['status']);
            $this -> response -> setCookies($this -> parseCookies($header));
            $this -> response -> setResponse($response);

            if(true === (json_last_error() == JSON_ERROR_NONE)) {
                $this -> response -> setJson($json);
            }
        }

        //Generate info
        $info = [

            'url' 			  => $url,
            'http_code' 	  => $headers['status']['code'],
            'http_method'	  => strtolower($this -> method),
            'header_size' 	  => strlen($header),
            'request_time' 	  => ['start' => $start, 'end' => $end, 'duration' => $end - $start],
            'request_content' => $options['content'],
            'request_headers' => $options['header']
        ];

        $this -> response -> setInfo($info);

        return $this;
    }


    /**
     * Convert files and fields to field and content
     * @return string
     */
    private function generateContent(): string {

        $content  = [];
        $boundary = str_repeat('-', 10) . str_shuffle(microtime(true) . uniqid());

        //Add files
        if(count($this -> files) > 0) {

            if(false === isset($this -> headers['Content-Type'])) {
                $this -> header('Content-Type', 'multipart/form-data; boundary=' . $boundary);
            }

            foreach($this -> files as $file) {

                $filename = $file -> name;
                $basename = $file -> file -> getBaseName();
                $mime 	  = $file -> mime ?? $file -> file -> getMime();

                $content[] = '--' . $boundary;
                $content[] = 'Content-Disposition: form-data; name="'. $filename .'"; filename="'. $basename .'"';
                $content[] = 'Content-Type: ' . $mime;
                $content[] = '';
                $content[] = $file -> file -> getContent();
                $content[] = '--' . $boundary;
            }
        }

        //Add form data / fields
        if(count($this -> fields) > 0) {

            if(false === isset($this -> headers['Content-Type'])) {
                $this -> header('Content-Type', 'multipart/form-data; boundary=' . $boundary);
            }

            $content[] = '--' . $boundary;

            foreach($this -> fields as $key => $value) {

                $content[] = 'Content-Disposition: form-data; name="'. $key .'"';
                $content[] = '';
                $content[] = $value;
                $content[] = '--' . $boundary;
            }
        }

        $content[] = $this -> body;

        return implode("\r\n", $content);
    }
}