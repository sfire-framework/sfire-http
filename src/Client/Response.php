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

use sFire\Entity\EntityAbstract;


/**
 * Class Response
 * @package sFire\Http
 */
class Response extends EntityAbstract {


    /**
     * Contains all the headers
     * @var array
     */
    private array $headers = [];


    /**
     * Contains information about the response status (code, text, etc.)
     * @var array
     */
    private array $status = [];


    /**
     * Contains the raw HTTP body
     * @var string
     */
    private ?string $body;


    /**
     * Contains an array as response if response is a valid JSON string
     * @var array
     */
    private ?array $json;


    /**
     * Contains all the response cookies
     * @var array
     */
    private array $cookies = [];


    /**
     * Contains the response (headers and body)
     * @var string
     */
    private ?string $response;


    /**
     * Contains additional information of the request
     * @var array $info
     */
    private array $info = [];


    /**
     * Set the response headers
     * @param array $headers
     * @return Response
     */
    public function setHeaders(array $headers): Response {

        $this -> headers = $headers;
        return $this;
    }


    /**
     * Set the response status
     * @param array $status
     * @return Response
     */
    public function setStatus(array $status): Response {

        $this -> status = $status;
        return $this;
    }


    /**
     * Set the response cookies
     * @param array $cookies
     * @return Response
     */
    public function setCookies(array $cookies): Response {

        $this -> cookies = $cookies;
        return $this;
    }


    /**
     * Set the raw HTTP body
     * @param string $body
     * @return Response
     */
    public function setBody(string $body): Response {

        $this -> body = $body;
        return $this;
    }


    /**
     * If response is a valid JSON string
     * @param array $json
     * @return Response
     */
    public function setJson($json): Response {

        $this -> json = $json;
        return $this;
    }


    /**
     * Set additional info from the request
     * @param array $info
     * @return Response
     */
    public function setInfo(array $info): Response {

        $this -> info = $info;
        return $this;
    }


    /**
     * Set the response from the request
     * @param string $response
     * @return Response
     */
    public function setResponse(string $response): Response {

        $this -> response = $response;
        return $this;
    }


    /**
     * Return the raw body from the HTTP request
    * @return null|string
    */
    public function getBody(): ?string {
        return $this -> body;
    }


    /**
     * Return an array converted from a valid JSON string from the HTTP request
    * @return null|array
    */
    public function getJson(): ?array {
        return $this -> json;
    }


    /**
     * Return the headers from the HTTP request
     * @return null|array
     */
    public function getHeaders(): ?array {
        return $this -> headers;
    }


    /**
     * Return the status from the HTTP request
     * @return null|array
     */
    public function getStatus(): ?array {
        return $this -> status;
    }


    /**
     * Return more info from the HTTP request
     * @return null|array
     */
    public function getInfo(): ?array {
        return $this -> info;
    }


    /**
     * Return all the cookies from the HTTP request
     * @return null|array
     */
    public function getCookies(): ?array {
        return $this -> cookies;
    }


    /**
     * Return response from the HTTP request
     * @return null|string
     */
    public function getResponse(): ?string {
        return $this -> response;
    }
}