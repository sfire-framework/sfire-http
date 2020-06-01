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

use sFire\Http\Exception\InvalidArgumentException;


/**
 * Class UrlParser
 * @package sFire\Http
 */
class UrlParser {


    /**
     * Represents the scheme of the url (http, https)
     * @var int
     */
    public const SCHEME = 0;


    /**
     * Represents the user of the url (i.e. admin)
     * @var int
     */
    public const USER = 1;


    /**
     * Represents the password of the url (i.e. admin@password)
     * @var int
     */
    public const PASSWORD = 2;


    /**
     * Represents all the sub domains of the url (cdn.mail.domain.com)
     * @var int
     */
    public const SUBDOMAIN = 3;


    /**
     * Represents domain of the url (domain.com)
     * @var int
     */
    public const DOMAIN	= 4;


    /**
     * Represents the domain extension of the url (i.e. .com)
     * @var int
     */
    public const EXTENSION = 5;


    /**
     * Represents the port number of the url (i.e. 80, 8080, 443)
     * @var int
     */
    public const PORT = 6;


    /**
     * Represents the path of the url (/customer/52)
     * @var int
     */
    public const PATH = 7;


    /**
     * Represents the query of the url (?id=52)
     * @var int
     */
    public const QUERY = 8;


    /**
     * Represents the fragment of the url (#id)
     * @var int
     */
    public const FRAGMENT = 9;


    /**
     * Contains a list with all the unique URL components
     * @param array
     */
    private array $components = [

        'scheme',
        'user',
        'password',
        'subDomain',
        'domain',
        'extension',
        'port',
        'path',
        'query',
        'fragment'
    ];


    /**
     * Contains the url as an stdClass
     * @var object
     */
    private object $url;


    /**
     * Constructor
     * @param string $url The URL that needs to be parsed
     */
    public function __construct(string $url) {

        //Trim the URL
        $url = trim($url);

        if($host = $this -> getParsedIp($url)) {
            $url = str_replace($host, '127.0.0.1', $url);
        }

        //For better parsing, add the scheme if non exists and then remove it after
        if(0 === preg_match('#^(.*?)://#', $url)) {

            $url = (object) parse_url('http://' . $url);
            unset($url -> scheme);
        }

        //Url starts with a scheme, so we can safely parse it
        else {
            $url = (object) parse_url($url);
        }

        $this -> url = (object) [

            'scheme' 	=> $url -> scheme ?? null,
            'user' 		=> $url -> user ?? null,
            'password' 	=> $url -> pass ?? null,
            'subDomain' => $this -> parseSubDomain($url),
            'domain' 	=> $this -> parseDomain($url),
            'extension' => $this -> parseExtension($url),
            'port' 		=> $url -> port ?? null,
            'path' 		=> $url -> path ?? null,
            'query' 	=> $url -> query ?? null,
            'fragment' 	=> $url -> fragment ?? null
        ];

        if(null !== $host) {
            $this -> url -> domain = $host;
        }
    }


    /**
     * Returns the parsed URL
     * @return object
     */
    public function getParsedUrl(): object {
        return $this -> url;
    }


    /**
     * Returns the scheme of the url
     * @return null|string
     */
    public function getScheme(): ?string {
        return $this -> url -> scheme ?? null;
    }


    /**
     * Set the scheme of the URL
     * @param string $scheme The scheme (http, https). Will be removed if set to null.
     * @return void
     */
    public function setScheme(?string $scheme): void {

        if(null !== $scheme) {

            if(0 === strlen(trim($scheme))) {
                $scheme = null;
            }
            else {
                $scheme = str_replace('://', '', $scheme);
            }
        }

        $this -> url -> scheme = $scheme;
    }


    /**
     * Returns the user of the url
     * @return null|string
     */
    public function getUser(): ?string {
        return $this -> url -> user ?? null;
    }


    /**
     * Set the user for authentication
     * @param string $user The username used for authentication. Will be removed if set to null.
     * @return void
     */
    public function setUser(?string $user): void {

        if(null !== $user && 0 === strlen(trim($user))) {
            $user = null;
        }

        $this -> url -> user = $user;
    }


    /**
     * Returns the password of the url
     * @return null|string
     */
    public function getPassword(): ?string {
        return $this -> url -> password ?? null;
    }


    /**
     * Set the password for authentication
     * @param string $password The password used for authentication. Will be removed if set to null.
     * @return void
     */
    public function setPassword(?string $password): void {

        if(null !== $password && 0 === strlen(trim($password))) {
            $password = null;
        }

        $this -> url -> password = $password;
    }


    /**
     * Retrieve the sub domain(s) from the URL
     * @return null|string
     */
    public function getSubDomain() {
        return $this -> url -> subDomain ?? null;
    }


    /**
     * Set the sub domain of a URL. Can be more then one.
     * @param null|string $subDomain The sub domain of a URL. Will be removed if set to null.
     * @return void
     */
    public function setSubDomain(?string $subDomain): void {

        if(null !== $subDomain && 0 === strlen(trim($subDomain))) {
            $subDomain = null;
        }

        $this -> url -> subDomain = $subDomain;
    }


    /**
     * Return the domain name from a URL
     * @return null|string
     */
    public function getDomain(): ?string {
        return $this -> url -> domain ?? null;
    }


    /**
     * Set the domain name of a URL
     * @param string $domain The domain name without extension. Will be removed if set to null.
     * @return void
     */
    public function setDomain(?string $domain): void {

        if(null !== $domain && 0 === strlen(trim($domain))) {
            $domain = null;
        }

        $this -> url -> domain = $domain;
    }


    /**
     * Return the extension of a URL
     * @return null|string
     */
    public function getExtension(): ?string {
        return $this -> url -> extension ?? null;
    }


    /**
     * Set the extension of a URL
     * @param string $extension Set the extension. Will be removed if set to null.
     * @return void
     */
    public function setExtension(?string $extension): void {

        if(null !== $extension && 0 === strlen(trim($extension))) {
            $extension = null;
        }

        $this -> url -> extension = $extension;
    }


    /**
     * Returns the port of the url
     * @return null|int
     */
    public function getPort(): ?int {
        return $this -> url -> port ? (int) $this -> url -> port : null;
    }


    /**
     * Set the port number to connect to
     * @param int $port The port number. Will be removed if set to null.
     * @return void
     * @throws InvalidArgumentException
     */
    public function setPort(?int $port): void {

        if($port < 0 || $port > 65535) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be between 0 and 65535, "%s" given', __METHOD__, $port));
        }

        $this -> url -> port = $port;
    }


    /**
     * Returns the path of the url
     * @return null|string
     */
    public function getPath(): ?string {
        return $this -> url -> path ?? null;
    }


    /**
     * Set the path of a URL
     * @param string $path The path of a URL. Will be removed if set to null.
     * @return void
     */
    public function setPath(?string $path): void {

        if(null !== $path && 0 === strlen(trim($path))) {
            $path = null;
        }

        $this -> url -> path = $path;
    }


    /**
     * Returns the query of the url
     * @return null|string
     */
    public function getQuery(): ?string {
        return $this -> url -> query ?? null;
    }


    /**
     * Set the query of a URL
     * @param string $query The query of a URL with or without leading question mark. Will be removed if set to null.
     * @return void
     */
    public function setQuery(?string $query): void {

        if(null !== $query) {

            if(null !== $query && 0 === strlen(trim($query))) {
                $query = null;
            }
            else {
                $query = ltrim($query, '?');
            }
        }

        $this -> url -> query = $query;
    }


    /**
     * Add a new parameter to the query URL
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addQueryValue(string $key, string $value): void {

        $query = (array) $this -> getParsedQuery();
        $query[$key] = $value;

        $this -> setQuery(http_build_query($query));
    }


    /**
     * Removes a existing parameter from the query URL
     * @param string $key
     * @return void
     */
    public function removeQueryValue(string $key): void {

        $query = (array) $this -> getParsedQuery();

        if(true === isset($query[$key])) {
            unset($query[$key]);
        }

        $this -> setQuery(http_build_query($query));
    }


    /**
     * Returns the fragment (after the #) of the url
     * @return null|string
     */
    public function getFragment(): ?string {
        return $this -> url -> fragment ?? null;
    }


    /**
     * Set the fragment of a URL
     * @param string $fragment The fragment of a URL with or without leading #. Will be removed if set to null.
     * @return void
     */
    public function setFragment(?string $fragment): void {

        if(null !== $fragment) {

            if(null !== $fragment && 0 === strlen(trim($fragment))) {
                $fragment = null;
            }
            else {
                $fragment = ltrim($fragment, '#');
            }
        }

        $this -> url -> fragment = $fragment;
    }


    /**
     * Returns the host (sub domains, domain and extension)
     * @return null|string
     */
    public function getHost(): ?string {
        return $this -> filter([self::SUBDOMAIN, self::DOMAIN, self::EXTENSION]);
    }


    /**
     * Returns the trimmed path of the url (without leading forward slashes)
     * @return null|string
     */
    public function getTrimmedPath() {
        return true === isset($this -> url -> path) ? ltrim($this -> url -> path, '/') : null;
    }


    /**
     * Parses the query string and converts it into an stdClass
     * @return object
     */
    public function getParsedQuery(): object {

        parse_str($this -> getQuery(), $query);
        return (object) $query;
    }


    /**
     * Filter URL components out of the current URL
     * @param array $filters Array with numbers representing URL components. 0 = scheme, 1 = user, 2 = password, 3 = subdomain, 4 = domain, 5 = extension, 6 = port, 7 = path, 8 = query, 9 = fragment
     * @return null|string
     */
    public function filter(array $filters): ?string {

        $url = '';

        foreach($this -> components as $index => $type) {

            if((true === is_string($type) && true === in_array($type, $filters, true)) || true === in_array($index, $filters, true)) {

                switch($index) {

                    case self::SCHEME    : $url .= $this -> getScheme() ? $this -> getScheme() . '://' : ''; break;
                    case self::USER      : $url .= $this -> getUser(); break;
                    case self::PASSWORD  : $url .= $this -> getPassword() ? ':' . $this -> getPassword() . '@' : ''; break;
                    case self::SUBDOMAIN : $url .= $this -> getSubDomain() ? $this -> getSubDomain() . '.' : ''; break;
                    case self::DOMAIN    : $url .= $this -> getDomain(); break;
                    case self::EXTENSION : $url .= $this -> getExtension() ? '.' . $this -> getExtension() : ''; break;
                    case self::PORT      : $url .= $this -> getPort() ? ':' . $this -> getPort() : ''; break;
                    case self::PATH      : $url .= $this -> getPath() ? '/' . ltrim($this -> getPath(), '/') : ''; break;
                    case self::QUERY     : $url .= $this -> getQuery() ? '?' . $this -> getQuery() : ''; break;
                    case self::FRAGMENT  : $url .= $this -> getFragment() ? '#' . $this -> getFragment() : ''; break;
                }
            }
        }

        return 0 === strlen($url) ? null : $url;
    }


    /**
     * Adds all the components of the url until the end or until the $till parameter has been reached to generate a URL
     * @param int $till Number representing a URL components. 0 = scheme, 1 = user, 2 = password, 3 = subdomain, 4 = domain, 5 = extension, 6 = port, 7 = path, 8 = query, 9 = fragment
     * @return null|string
     */
    public function generate(int $till = self::FRAGMENT): ?string {

        $components = [];

        for($i = 0; $i <= $till; $i++) {
            $components[] = $i;
        }

        return $this -> filter($components);
    }


    /**
     * Parse a URL object and return the sub domain(s) from the url
     * @param object $url A Url object parsed by parse_url function
     * @return string|null
     */
    private function parseSubDomain(object $url): ?string {

        if(true === isset($url -> host) && true === is_string($url -> host)) {

            if($host = $this -> getParsedIp($url -> host)) {

                $subdomains = explode($host, $url -> host);

                if(count($subdomains) > 1) {
                    return rtrim($subdomains[0], '.');
                }
            }

            $host = explode('.', $url -> host);
            $subDomains = array_slice($host, 0, count($host) - 2);

            if(0 === count($subDomains)) {
                return null;
            }

            return implode('.', $subDomains);
        }

        return null;
    }


    /**
     * Parse a URL object and return the domain from the url
     * @param object $url A Url object parsed by parse_url function
     * @return string|null
     */
    private function parseDomain(object $url): ?string {

        if(true === isset($url -> host) && true === is_string($url -> host)) {

            if($ip = $this -> getParsedIp($url -> host)) {
                return $ip;
            }

            $host   = explode('.', $url -> host);
            $domain = array_slice($host, 0, count($host) - 1);
            $domain = end($domain);

            if(false === $domain) {
                return $url -> host;
            }

            return $domain;
        }

        return null;
    }


    /**
     * Parse a URL object and return the extension from the url
     * @param object $url A Url object parsed by parse_url function
     * @return string|null
     */
    private function parseExtension(object $url): ?string {

        if(true === isset($url -> host) && true === is_string($url -> host)) {

            if($host = $this -> getParsedIp($url -> host)) {

                $subdomains = explode($host, $url -> host);

                if(count($subdomains) > 1) {
                    return ltrim($subdomains[1], '.');
                }
            }

            $chunks = explode('.', $url -> host);
            $extension = end($chunks);

            if($extension === $this -> getDomain()) {
                return null;
            }

            return $extension;
        }

        return null;
    }


    /**
     * Parses a string and returns an IPv4 or IPv6 ip address if matched
     * @param string $string A string that needs to be checked for ip address
     * @return null|string
     */
    private function getParsedIp(string $string): ?string {

        //Check for IP version 4
        if(true === (bool) preg_match('/((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/', $string, $ip)) {
            return $ip[0];
        }

        //Check for IP version 6
        if(true === (bool) preg_match('/\[(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))]/', $string, $ip)) {
            return $ip[0];
        }

        return null;
    }
}