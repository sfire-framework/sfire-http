<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use sFire\Http\UrlParser;


/**
 * Class UrlParserTest
 */
final class UrlParserTest extends TestCase {


    /**
     * Contains a list with all the unique URL components
     */
    private $components = [
    
        'scheme'    => 'https', 
        'user'      => 'username', 
        'password'  => 'password2', 
        'subDomain' => 'test.test',
        'domain'    => 'example2', 
        'extension' => 'org', 
        'port'      => 8081,
        'path'      => 'blog', 
        'query'     => 'test=true&mode=testing',
        'fragment'  => 'anchor'
    ];


    /**
     * Test if all URL getters are working to get individual URL components like scheme or port
     * @return void
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIfURLGettersAreWorking(): void {

        require('assets' . DIRECTORY_SEPARATOR . 'urls.php');

        foreach($urls as $url => $components) {

            $entity = new UrlParser($url);

            foreach($components as $component => $value) {
                $this -> assertEquals($entity -> {'get' . ucfirst($component)}(), $value);
            }
        }
    }


    /**
     * Test if all URL setters are working to set individual URL components like scheme or port
     * @return void
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIfURLSettersAreWorking(): void {

        require('assets' . DIRECTORY_SEPARATOR . 'urls.php');

        foreach($urls as $url => $components) {

            $entity = new UrlParser($url);

            foreach($this -> components as $component => $value) {

                $entity -> {'set' . ucfirst($component)}($value);
                $this -> assertEquals($entity -> {'get' . ucfirst($component)}(), $value);
            }
        }
    }


    /**
     * Test if all URL are generated properly
     * @return void
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIfURLGeneratorWorks(): void {

        require('assets' . DIRECTORY_SEPARATOR . 'generate.php');

        foreach($urls as $url => $generated) {

            $entity = new UrlParser($url);

            for($i = 0; $i <= 9; $i++) {
                $this -> assertEquals($entity -> generate($i), $generated[$i]);
            }
        }
    }


    /**
     * Test if retrieving the path component of a URL is not beginning with a forward slash
     * @return void
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testTrimmingTheUrlPath(): void {

        $entity = new UrlParser('http://example.com/path/');
        $this -> assertEquals($entity -> getTrimmedPath(), 'path/');
    }


    /**
     * Test if a URL query is successfully parsed
     * @return void
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testParsingTheUrlQuery(): void {

        $entity = new UrlParser('http://example.com/?foo=bar&baz=quez');
        $query  = $entity -> getParsedQuery();

        $this -> assertEquals($query, (object) [

            'foo' => 'bar',
            'baz' => 'quez'
        ]);
    }


    /**
     * Test if a URL is successfully parsed
     * @return void
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testParsingTheUrl(): void {
        
        $entity = new UrlParser('http://user:pass@mail.sub.example.com:8080/path?foo=bar&baz=quez#fragment');
        $url    = $entity -> getParsedUrl();

        $this -> assertEquals($url, (object) [
            
            'scheme'    => 'http',
            'user'      => 'user',
            'password'  => 'pass',
            'subDomain' => 'mail.sub',
            'domain'    => 'example',
            'extension' => 'com',
            'port'      => 8080,
            'path'      => '/path',
            'query'     => 'foo=bar&baz=quez',
            'fragment'  => 'fragment',
        ]);
    }
}