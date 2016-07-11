<?php

  namespace Test\Xparse\Parser;

  use GuzzleHttp\Client;
  use GuzzleHttp\Cookie\CookieJarInterface;
  use GuzzleHttp\Handler\MockHandler;
  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\Parser\Parser;

  /**
   *
   * @package Xparse\Parser\Test
   */
  class ParserTest extends \PHPUnit_Framework_TestCase {

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

      $page = $parser->post('http://test.com/info', '123');

      self::assertInstanceOf(get_class(new \Xparse\ElementFinder\ElementFinder('<html></html>')), $page);
      self::assertEquals($page, $parser->getLastPage());
    }


    /**
     * @return Client
     */
    protected function getDemoClient() {
      /** @noinspection HtmlUnknownTarget */
      $mock = new MockHandler(
        [
          new Response(
            200,
            ['X-GUZZLE-EFFECTIVE-URL' => 'http://test.com/url/'],
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
</html>
'
          ),
        ]
      );
      return new Client(['handler' => $mock]);
    }


    public function testGetResponseReasonPhrase() {
      $parser = new Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $parser->get($url);
      self::assertEquals('OK', $parser->getLastResponse()->getReasonPhrase());
    }


    public function testGetEffectiveUrlFromHeaders() {
      $parser = new Parser($this->getDemoClient());
      $url = 'http://test.com/some-url/';
      $parser->get($url);
      self::assertEquals('http://test.com/url/', $parser->getLastResponse()->getHeaderLine('X-GUZZLE-EFFECTIVE-URL'));
    }


    public function testConvertElementFinderUrls() {
      $parser = new Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $page = $parser->get($url);
      self::assertInstanceOf(ElementFinder::class, $page);
      $firstUrl = $page->value('//a/@href')->getFirst();
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
      $parser->get(null);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPostWithInvalidParams() {
      $parser = new Parser($this->getDemoClient());
      static::assertNotEmpty($parser);
      $parser->post(new \stdClass(), null);
    }

  }
