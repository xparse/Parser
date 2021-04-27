<?php

declare(strict_types=1);

namespace Test\Xparse\Parser;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\ElementFinder;
use Xparse\Parser\Parser;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ParserTest extends TestCase
{

    public function testInit(): void
    {
        $parser = new Parser();
        self::assertEquals(get_class(new Client()), get_class($parser->getClient()));
    }


    public function testGet(): void
    {
        $client = $this->getDemoClient();
        $parser = new Parser($client);

        self::assertEquals($client, $parser->getClient());

        $page = $parser->get('http://test.com');

        self::assertEquals($page, $parser->getLastPage());
    }

    /**
     */
    protected function getDemoClient(): Client
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    [],
                    '<!DOCTYPE html>
              <html>
                <head lang="en">
                  <meta charset="UTF-8">
                  <title></title>
                </head>
                <body>
                  <a href="index.html">link</a>
                  <div>Text text; Текст кирилица</div>
                </body>
              </html>'
                ),
            ]
        );

        return new Client(['handler' => $mock]);
    }

    public function testPost(): void
    {
        $client = $this->getDemoClient();
        $parser = new Parser($client);

        self::assertEquals($client, $parser->getClient());

        $page = $parser->post('http://test.com/info', [RequestOptions::FORM_PARAMS => ['123']]);

        self::assertEquals($page, $parser->getLastPage());
    }

    public function testGetResponseReasonPhrase(): void
    {
        $parser = new Parser($this->getDemoClient());
        $url = 'http://test.com/url/';
        $parser->get($url);
        $response = $parser->getLastResponse();

        self::assertEquals('OK', $response->getReasonPhrase());
    }


    public function testGetEffectiveUrlFromHeaders(): void
    {
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['X-GUZZLE-EFFECTIVE-URL' => 'http://test.com/effective-url/'],
                    '<!DOCTYPE html>
              <html>
                <head lang="en">
                  <meta charset="UTF-8">
                </head>
                <body></body>
              </html>
            '
                ),
            ]
        );
        $parser = new Parser(new Client(['handler' => $mock]));
        $url = 'http://test.com/url/';
        $parser->get($url);
        $effectiveUrl = $parser->getLastResponse()->getHeaderLine('X-GUZZLE-EFFECTIVE-URL');
        self::assertNotEquals($url, $effectiveUrl);
        self::assertEquals('http://test.com/effective-url/', $effectiveUrl);
    }


    public function testConvertElementFinderUrls(): void
    {
        $parser = new Parser($this->getDemoClient());
        $url = 'http://test.com/url/';
        $page = $parser->get($url);
        self::assertInstanceOf(ElementFinder::class, $page);
        $firstUrl = $page->value('//a/@href')->first();
        self::assertEquals($url . 'index.html', $firstUrl);
    }


    public function testDefaultClientConfiguration(): void
    {
        self::assertInstanceOf(
            CookieJarInterface::class,
            (new Parser())->getClient()->getConfig('cookies')
        );
    }


    public function testRetrieveElementFinderFactory(): void
    {
        $parser = new Parser();
        self::assertNotNull($parser->getElementFinderFactory());
    }


    public function testGetInvalidUrl(): void
    {
        self::expectException(InvalidArgumentException::class);
        $parser = new Parser($this->getDemoClient());
        self::assertNotEmpty($parser);
        $parser->get('');
    }


    public function testPostWithInvalidParams(): void
    {
        self::expectException(InvalidArgumentException::class);
        $parser = new Parser($this->getDemoClient());
        self::assertNotEmpty($parser);
        $parser->post('', ['someData']);
    }

}
