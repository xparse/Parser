<?php

  declare(strict_types=1);

  namespace Test\Xparse\Parser;

  use GuzzleHttp\Client;
  use GuzzleHttp\Cookie\CookieJarInterface;
  use GuzzleHttp\Handler\MockHandler;
  use GuzzleHttp\Psr7\Response;
  use GuzzleHttp\RequestOptions;
  use PHPUnit\Framework\TestCase;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\Parser\Parser;

  class ParserTest extends TestCase {

    public function testInit() {

      $parser = new Parser();
      self::assertEquals(get_class(new Client()), get_class($parser->getClient()));
    }


    public function testGet() {

      $client = $this->getDemoClient();
      $parser = new Parser($client);

      self::assertEquals($client, $parser->getClient());

      $page = $parser->get('http://test.com');

      self::assertInstanceOf(get_class(new \Xparse\ElementFinder\ElementFinder('<html><a>1</a></html>')), $page);
      self::assertEquals($page, $parser->getLastPage());
    }


    public function testPost() {

      $client = $this->getDemoClient();
      $parser = new Parser($client);

      self::assertEquals($client, $parser->getClient());

      $page = $parser->post('http://test.com/info', [RequestOptions::FORM_PARAMS => ['123']]);

      self::assertInstanceOf(get_class(new \Xparse\ElementFinder\ElementFinder('<html></html>')), $page);
      self::assertEquals($page, $parser->getLastPage());
    }


    /**
     * @return Client
     */
    protected function getDemoClient() : Client {
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


    public function testGetResponseReasonPhrase() {
      $parser = new Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $parser->get($url);
      $response = $parser->getLastResponse();

      self::assertEquals('OK', $response->getReasonPhrase());
    }


    public function testGetEffectiveUrlFromHeaders() {
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
      $response = $parser->getLastResponse();
      $effectiveUrl = $response->getHeaderLine('X-GUZZLE-EFFECTIVE-URL');
      self::assertNotEquals($url, $effectiveUrl);
      self::assertEquals('http://test.com/effective-url/', $effectiveUrl);
    }


    public function testConvertElementFinderUrls() {
      $parser = new Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $page = $parser->get($url);
      self::assertInstanceOf(ElementFinder::class, $page);
      $firstUrl = $page->value('//a/@href')->first();
      self::assertEquals($url . 'index.html', $firstUrl);
    }


    public function testDefaultClientConfiguration() {
      $parser = new Parser();
      $option = $parser->getClient()->getConfig('cookies');
      self::assertInstanceOf(CookieJarInterface::class, $option);

    }


    public function testRetrieveElementFinderFactory() {
      $parser = new Parser();
      static::assertNotNull($parser->getElementFinderFactory());
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidUrl() {
      $parser = new Parser($this->getDemoClient());
      static::assertNotEmpty($parser);
      $parser->get('');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPostWithInvalidParams() {
      $parser = new Parser($this->getDemoClient());
      static::assertNotEmpty($parser);
      $parser->post('', ['someData']);
    }

  }
