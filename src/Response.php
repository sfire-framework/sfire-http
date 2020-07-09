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

use sFire\Http\Exception\RuntimeException;
use sFire\FileControl\File;


/**
 * Class Response
 * @package sFire\Http
 */
class Response {


    /**
     * Contains instance of self
     * @var null|self
     */
    private static ?self $instance = null;


    /**
     * Returns instance of the Response object
     * @return self
     */
    public static function getInstance(): self {

        if(null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }


    /**
     * Sets the response header by name and value
     * @param string $name The name of the header
     * @param string $value The value of the header
     * @param int $code Optional HTTP status code
     * @return void
     */
    public static function addHeader(string $name, string $value, int $code = null): void {

        if(null === $code) {

            header($name . ':' . $value, true);
            return;
        }

        header($name . ':' . $value, true, $code);
    }


    /**
     * Remove response header by header name
     * @param string $name The name of the header
     * @return void
     */
    public static function removeHeader(string $name): void {
        header_remove($name);
    }


    /**
     * Remove all response headers
     * @return void
     */
    public static function flushHeaders(): void {
        header_remove();
    }


    /**
     * Give a file for the client to download
     * @param string $file A path to a file
     * @param string $filename [Optional] filename
     * @param string $mime [Optional] MIME type
     * @return void
     * @throws RuntimeException
     */
    public static function addFile(string $file, string $filename = null, string $mime = null): void {

        $file = new File($file);

        if(false === $file -> exists()) {
            throw new RuntimeException(sprintf('File "%s" does not exists', $file -> getPath()));
        }

        $filename       = $filename ?? $file -> getBasename();
        $mime           = $mime ?? $file -> getMimeType();
        $offset         = 0;
        $fileSize       = $file -> getFileSize();
        $length         = $fileSize;
        $partialContent = false;

        //Dealing with partial content if the HTTP range header
        if(true === isset($_SERVER['HTTP_RANGE'])) {

            preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches);

            $offset         = intval($matches[1]);
            $length         = intval($matches[2]) - $offset;
            $partialContent = true;
        }

        //Check if request supports partial content
        if(true === $partialContent) {

            static :: setStatus(206);
            static :: addHeader('Content-Range', 'bytes ' . $offset . '-' . ($offset + $length) . '/' . $fileSize);
        }

        //Add content type header
        if(null !== $mime) {
            static :: addHeader('Content-Type', $mime);
        }

        static :: addHeader('Content-Length', $fileSize);
        static :: addHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        static :: addHeader('Accept-Ranges','bytes');
        static :: addHeader('Content-Encoding','none');

        $file    = fopen($file -> getPath(), 'r');
        $current = $offset;

        fseek($file, $offset, 0);

        while($current <= $length && connection_status() == 0) {

            print fread($file, min(1024 * 16, ($length - $current) + 1));
            $current += 1024 * 16;
        }
    }


    /**
     * Sets the HTTP response status by code with optional custom status text
     * @param int $code The HTTP status code
     * @return bool TRUE if HTTP status code is found, FALSE if not
     */
    public static function setStatus(int $code): bool {

        $status = [

            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints (RFC 8297)',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information (since HTTP/1.1)',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            420 => 'Method Failure',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            440 => 'Login Time-out',
            444 => 'No Response',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls (Microsoft)',
            451 => 'Unavailable For Legal Reasons',
            494 => 'Request header too large',
            495 => 'SSL Certificate Error',
            496 => 'SSL Certificate Required',
            497 => 'HTTP Request Sent to HTTPS Port',
            498 => 'Invalid Token',
            499 => 'Client Closed Request',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            520 => 'Web Server Returned an Unknown Error',
            521 => 'Web Server Is Down',
            522 => 'Connection Timed Out',
            523 => 'Origin Is Unreachable',
            524 => 'A Timeout Occurred',
            525 => 'SSL Handshake Failed',
            526 => 'Invalid SSL Certificate',
            527 => 'Railgun Error',
            530 => 'Site is frozen',
            598 => 'Network read timeout error'
        ];

        if(true === isset($status[$code])) {

            $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';

            header(sprintf('%s %s %s', $protocol, $code, $status[$code]));
            return true;
        }

        return false;
    }
}