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

use sFire\DataControl\Translators\StringTranslator;
use sFire\DataControl\TypeArray;


/**
 * Class Request
 * @package sFire\Http
 */
class Request {


    /**
     * Contains instance of self
     * @var null|self
     */
    private static ?self $instance = null;


    /**
     * Contains the current method with its data
     * @var array
     */
    private static array $method = [];


    /**
     * Contain all the options like trim strings, empty array to null and empty string to null
     * @var array
     */
    private static array $options = [];


    /**
     * Returns instance of Request
     * @return self
     */
    public static function getInstance(): self {

        if(null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }


    /**
     * Get variable from GET
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromGet($key = null, $default = null) {
        return static :: from('get', $key, $default, $_GET);
    }


    /**
     * Get variable from POST
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromPost($key = null, $default = null) {
        return static :: from('post', $key, $default, $_POST);
    }


    /**
     * Get variable from PUT
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromPut($key = null, $default = null) {
        return static :: from('put', $key, $default);
    }


    /**
     * Get variable from DELETE
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromDelete($key = null, $default = null) {
        return static :: from('delete', $key, $default);
    }


    /**
     * Get variable from PATCH
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromPatch($key = null, $default = null) {
        return static :: from('patch', $key, $default);
    }


    /**
     * Get variable from CONNECT
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromConnect($key = null, $default = null) {
        return static :: from('connect', $key, $default);
    }


    /**
     * Get variable from HEAD
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromHead($key = null, $default = null) {
        return static :: from('head', $key, $default);
    }


    /**
     * Get variable from OPTIONS
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromOptions($key = null, $default = null) {
        return static :: from('options', $key, $default);
    }


    /**
     * Get variable from TRACE
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromTrace($key = null, $default = null) {
        return static :: from('trace', $key, $default);
    }


    /**
     * Get variable from the current HTTP method
     * @param mixed $key The name of the data
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    public static function fromCurrent($key = null, $default = null) {

        switch(static :: getMethod()) {

            case 'get' : $data = $_GET; break;
            case 'post' : $data = $_POST; break;
            default : $data = null;
        }

        return static :: from(static :: getMethod(), $key, $default, $data);
    }


    /**
     * Get body from current HTTP method
     * @return mixed
     */
    public static function getBody() {
        return @file_get_contents('php://input');
    }


    /**
     * Get all request headers
     * @return array
     */
    public static function getHeaders(): array {
        return function_exists('getallheaders') ? getallheaders() : [];
    }


    /**
     * Get the user agent from the request
     * @return string|null
     */
    public static function getUserAgent(): ?string {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }


    /**
     * Get the connection type from the request
     * @return null|string
     */
    public static function getConnection(): ?string {
        return $_SERVER['HTTP_CONNECTION'] ?? null;
    }


    /**
     * Get the cache control from the request
     * @return null|string
     */
    public static function getCacheControl(): ?string {
        return $_SERVER['HTTP_CACHE_CONTROL'] ?? null;
    }


    /**
     * Get the accept from the request
     * @return null|string
     */
    public static function getAccept(): ?string {
        return $_SERVER['HTTP_ACCEPT'] ?? null;
    }


    /**
     * Get the accepted encoding from the request
     * @return null|string
     */
    public static function getAcceptedEncoding(): ?string {
        return $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null;
    }


    /**
     * Get the accepted language from the request
     * @return null|string
     */
    public static function getAcceptedLanguage(): ?string {
        return $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
    }


    /**
     * Get the authentication type from the request
     * @return null|string
     */
    public static function getAuthentication(): ?string {
        return $_SERVER['AUTH_TYPE'] ?? null;
    }


    /**
     * Get the protocol from the request
     * @return string
     */
    public static function getProtocol(): ?string {
        return $_SERVER['SERVER_PROTOCOL'] ?? null;
    }


    /**
     * Get the user from the request
     * @return null|string
     */
    public static function getUser(): ?string {
        return $_SERVER['PHP_AUTH_USER'] ?? null;
    }


    /**
     * Get the password from the request
     * @return null|string
     */
    public static function getPassword(): ?string {
        return $_SERVER['PHP_AUTH_PW'] ?? null;
    }


    /**
     * Get the character set from the request
     * @return null|string
     */
    public static function getCharacterSet(): ?string {
        return $_SERVER['HTTP_ACCEPT_CHARSET'] ?? null;
    }


    /**
     * Get the request time
     * @return null|string
     */
    public static function getRequestTime(): ?string {
        return $_SERVER['REQUEST_TIME_FLOAT'] ?? null;
    }


    /**
     * Get the referer from the request
     * @return null|string
     */
    public static function getReferer(): ?string {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }


    /**
     * Get the content length from the request
     * @return null|string
     */
    public static function getContentLength(): ?string {
        return $_SERVER['CONTENT_LENGTH'] ?? null;
    }


    /**
     * Get the scheme from the request
     * @return null|string
     */
    public static function getScheme(): ?string {
        return $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME'] ?? null;
    }


    /**
     * Get the request uri from the request
     * @return null|string
     */
    public static function getUri(): ?string {
        return $_SERVER['REQUEST_URI'] ?? null;
    }


    /**
     * Get the host from the request
     * @return null|string
     */
    public static function getHost(): ?string {
        return $_SERVER['HTTP_X_FORWARDED_HOST']  ?? $_SERVER['HTTP_HOST'] ?? null;
    }


    /**
     * Returns the port from the current request
     * @return null|int
     */
    public static function getPort(): ?int {

        $port = $_SERVER['HTTP_X_FORWARDED_PORT'] ?? $_SERVER['SERVER_PORT'] ?? null;

        if(null !== $port) {
            return (int) $port;
        }

        return null;
    }


    /**
     * Returns all the uploaded files in a more readable format from the $_FILES array
     * @param bool $skipEmpty Skip empty files
     * @return array
     */
    public static function getUploadedFiles(bool $skipEmpty = true): array {

        $result = [];

        foreach($_FILES as $field => $data) {

            foreach($data as $key => $val) {

                $result[$field] = [];

                if(false === is_array($val)) {
                    $result[$field] = $data;
                }
                else {

                    $res = [];
                    TypeArray :: flip($res, [], $data);
                    $result[$field] += $res;
                }
            }
        }

        if(true === $skipEmpty) {

            $files  = static :: filterEmptyUploadedFiles($result);
            $result = [];
            
            foreach($files as $file) {
                TypeArray :: insertIntoArray($result, $file['path'], $file['file']);
            }
        }

        return $result;
    }


    /**
     * Get request header by header name
     * @param string $header The name of the header
     * @return null|string
     */
    public static function getHeader(string $header): ?string {

        $headers = static :: getHeaders();

        if(true === isset($headers[$header])) {
            return $headers[$header];
        }

        return null;
    }


    /**
     * Returns the url from current request
     * @return null|string
     */
    public static function getUrl(): ?string {

        $url = new UrlParser(static :: getHost() . static :: getUri());
        $url -> setScheme(static :: getScheme());

        return $url -> generate();
    }


    /**
     * Check if header exists
     * @param string $header The name of the header
     * @return bool
     */
    public static function hasHeader(string $header): bool {

        $headers = static :: getHeaders();
        return true === isset($headers[$header]);
    }


    /**
     * Returns the Authorization header
     * @return string|null
     */
    public static function getAuthorizationHeader(): ?string {

        $header = null;

        if(true === isset($_SERVER['Authorization'])) {
            $header = trim($_SERVER['Authorization']);
        }

        //Nginx or fast CGI
        elseif(true === isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        //Apache
        elseif(true === function_exists('apache_request_headers')) {

            $requestHeaders = apache_request_headers();

            //Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if(true === isset($requestHeaders['Authorization'])) {
                $header = trim($requestHeaders['Authorization']);
            }
        }

        return $header;
    }


    /**
     * Returns token from authentication header based on a given token type (i.e. Bearer)
     * @return mixed|null
     */
    public function getAuthorizationToken(string $type) {

        $header = static::getAuthorizationHeader();

        if(null === $header) {
            return null;
        }

        if(true === (bool) preg_match('/'. $type .'\s(?<token>\S+)/', $header, $matches)) {
            return $matches['token'];
        }

        return null;
    }


    /**
     * Return all the data from get, post, put, delete, patch, connect, head, options and trace
     * @return array
     */
    public static function all(): array {

        if(true === isset(static::$method['data'])) {
            return static::$method['data'];
        }

        parse_str(static :: getBody(), $vars);

        return [

            'get' 	 	=> $_GET,
            'post'	 	=> $_POST,
            'put' 	 	=> static :: isMethod('put') 	 ? 	$vars : [],
            'delete' 	=> static :: isMethod('delete')  ? 	$vars : [],
            'patch'  	=> static :: isMethod('patch') 	 ? 	$vars : [],
            'connect'  	=> static :: isMethod('connect') ? 	$vars : [],
            'head'  	=> static :: isMethod('head')	 ? 	$vars : [],
            'options'  	=> static :: isMethod('options') ? 	$vars : [],
            'trace'  	=> static :: isMethod('trace') 	 ? 	$vars : []
        ];
    }


    /**
     * Check if request method equals the given method
     * @param string $method The method name
     * @return bool
     */
    public static function isMethod(string $method = null): bool {
        return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) == strtolower(trim($method)) : false;
    }


    /**
     * Check if request method is GET
     * @return bool Returns TRUE if current method is get, FALSE if not
     */
    public static function isGet(): bool {
        return static :: isMethod('get');
    }


    /**
     * Check if request method is POST
     * @return bool Returns TRUE if current method is post, FALSE if not
     */
    public static function isPost(): bool {
        return static :: isMethod('post');
    }


    /**
     * Check if request method is PUT
     * @return bool Returns TRUE if current method is put, FALSE if not
     */
    public static function isPut(): bool {
        return static :: isMethod('put');
    }


    /**
     * Check if request method is DELETE
     * @return bool Returns TRUE if current method is delete, FALSE if not
     */
    public static function isDelete(): bool {
        return static :: isMethod('delete');
    }


    /**
     * Check if request method is PATCH
     * @return bool Returns TRUE if current method is patch, FALSE if not
     */
    public static function isPatch(): bool {
        return static :: isMethod('patch');
    }


    /**
     * Check if request method is CONNECT
     * @return bool Returns TRUE if current method is connect, FALSE if not
     */
    public static function isConnect(): bool {
        return static :: isMethod('connect');
    }


    /**
     * Check if request method is HEAD
     * @return bool Returns TRUE if current method is head, FALSE if not
     */
    public static function isHead(): bool {
        return static :: isMethod('head');
    }


    /**
     * Check if request method is OPTIONS
     * @return bool Returns TRUE if current method is options, FALSE if not
     */
    public static function isOptions(): bool {
        return static :: isMethod('options');
    }


    /**
     * Check if request method is TRACE
     * @return bool Returns TRUE if current method is trace, FALSE if not
     */
    public static function isTrace(): bool {
        return static :: isMethod('trace');
    }


    /**
     * Return the request method
     * @return string
     */
    public static function getMethod(): ?string {
        return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : null;
    }


    /**
     * Set the current request method
     * @param string $method The name of the method
     * @return void
     */
    public static function setMethod(string $method): void {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
    }


    /**
     * Set the current URI
     * @param string $uri The URI
     */
    public static function setUri(string $uri): void {
        $_SERVER['REQUEST_URI'] = $uri;
    }


    /**
     * Set the HTTP host
     * @param string $host The HTTP host
     */
    public static function setHost(string $host): void {
        $_SERVER['HTTP_HOST'] = $host;
    }


    /**
     * Set the Request Scheme
     * @param string $scheme The request scheme
     */
    public static function setScheme(string $scheme): void {

        $types = ['HTTP_X_FORWARDED_PROTO'];

        foreach($types as $protocol) {

            if(true === isset($_SERVER[$protocol])) {
                $_SERVER[$protocol] = strtolower($scheme);
            }
        }

        $_SERVER['REQUEST_SCHEME'] = $scheme;
    }


    /**
     * Returns the request IP address
     * @return null|string
     */
    public static function getIp(): ?string {

        if('cli' === php_sapi_name()) {
            return getHostByName(php_uname('n'));
        }

        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['HTTP_VIA'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    }


    /**
     * Returns UrlParser instance with current request url
     * @return null|UrlParser
     */
    public static function getParsedUrl(): ?UrlParser {

        if($url = static :: getUrl()) {

            $url = new UrlParser($url);
            $url -> setPort(static :: getPort());

            return $url;
        }

        return null;
    }


    /**
     * Returns variable from source while converting an array from string
     * @param mixed $key The key that needs to be found
     * @param mixed $default Will return the given default value if data is not found
     * @return mixed
     */
    private static function get($key = null, $default = null) {

        if(null !== $key && isset(static::$method['method'], static::$method['data'])) {

            if(true === static :: isMethod(static::$method['method'])) {

                $translator = new StringTranslator(static::$method['data']);
                $data       = $translator -> get($key);
                $default    = (null === $data ? $default : $data);
            }

            static::$method = [];
        }

        return $default;
    }


    /**
     * Get variable from variable source
     * @param string $type The HTTP method
     * @param mixed $key The key that needs to be found
     * @param mixed $default Will return the given default value if data is not found
     * @param array $source Arbitrary data source
     * @return mixed
     */
    private static function from(string $type, $key, $default, array &$source = null) {

        if(null === $source) {

            if(static :: isMethod($type)) {

                $source = [];
                static :: parseRawHttpRequest($source);
            }
        }

        static::$method = ['method' => $type, 'data' => &$source];

        if(null !== $key) {
            return static :: get($key, $default);
        }

        return $source;
    }


    /**
     * Parses raw HTTP request string
     * @param array $data
     * @return null|array
     */
    private static function parseRawHttpRequest(array &$data): ?array {

        $_SERVER['CONTENT_TYPE'] ??= '';

        $input = static :: getBody();

        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

        if(0 === count($matches)) {

            $json = @json_decode($input, true);

            if(true === (json_last_error() == JSON_ERROR_NONE)) {

                $data = $json;
                return $json;
            }

            parse_str(urldecode($input), $data);
            return $data;
        }

        $boundary 	= $matches[1];
        $blocks 	= preg_split("/-+$boundary/", $input);

        array_pop($blocks);

        foreach($blocks as $id => $block) {

            if('' === $block) {
                continue;
            }

            if(false !== strpos($block, 'application/octet-stream')) {

                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                $data['files'][$matches[1]] = $matches[2];
            }
            else {

                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                $data[$matches[1]] = $matches[2];
            }
        }

        return null;
    }


    /**
     * @param $data
     * @param array $keys
     * @param array $files
     * @return array
     */
    private static function filterEmptyUploadedFiles($data, $keys = [], $files = []) {

        if(true === is_array($data)) {

            $file = true;

            foreach(['name', 'type', 'tmp_name', 'error', 'size'] as $type) {

                if(false === array_key_exists($type, $data)) {

                    $file = false;
                    break;
                }
            }

            if(true === $file) {

                if(strlen($data['name']) > 0 && strlen($data['tmp_name']) > 0) {
                    $files[] = ['path' => $keys, 'file' => $data];
                }
            }
            else {

                foreach($data as $key => $value) {

                    $keys[] = $key;
                    $files = array_merge($files, static :: filterEmptyUploadedFiles($value, $keys));
                    array_pop($keys);
                }
            }
        }

        return $files;
    }
}