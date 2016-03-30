<?php

  namespace Test\Xparse\Parser;

  use GuzzleHttp\Client;
  use GuzzleHttp\Handler\MockHandler;
  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;

  /**
   *
   * @package Xparse\Parser\Test
   */
  class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testInit() {

      $parser = new \Xparse\Parser\Parser();
      $this->assertEquals(get_class(new Client()), get_class($parser->getClient()));
    }


    public function testGet() {

      $client = $this->getDemoClient();
      $parser = new \Xparse\Parser\Parser($client);

      $this->assertEquals($client, $parser->getClient());

      $page = $parser->get('http://test.com');

      $this->assertInstanceOf(get_class(new \Xparse\ElementFinder\ElementFinder("<html><a>1</a></html>")), $page);
      $this->assertEquals($page, $parser->getLastPage());
    }


    public function testPost() {

      $client = $this->getDemoClient();
      $parser = new \Xparse\Parser\Parser($client);

      $this->assertEquals($client, $parser->getClient());

      $page = $parser->post('http://test.com/info', ['data' => '123']);

      $this->assertInstanceOf(get_class(new \Xparse\ElementFinder\ElementFinder("<html></html>")), $page);
      $this->assertEquals($page, $parser->getLastPage());
    }


    /**
     * @return Client
     */
    protected function getDemoClient() {
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
</html>
'
          ),
        ]
      );
      return new Client(['handler' => $mock]);
    }


    public function testGetResponseReasonPhrase() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $parser->get($url);
      $this->assertEquals('OK', $parser->getLastResponse()->getReasonPhrase());
    }


    public function testConvertElementFinderUrls() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $page = $parser->get($url);
      $this->assertInstanceOf(ElementFinder::class, $page);
      $firstUrl = $page->attribute('//a/@href')->getFirst();
      $this->assertEquals($url . 'index.html', $firstUrl);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidUrl() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $parser->get(null);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPostWithInvalidParams() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $parser->post(new \stdClass(), null);
    }

  }
