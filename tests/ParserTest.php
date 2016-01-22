<?php

  namespace Test\Xparse\Parser;

  use GuzzleHttp\Client;
  use GuzzleHttp\Handler\MockHandler;
  use GuzzleHttp\Psr7\Response;

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
     * @param $url
     * @return string
     */
    protected function getHtmlData($url) {
      $html = file_get_contents(__DIR__ . '/data' . $url);
      return $html;
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
            $this->getHtmlData('/test-get.html')
          ),
        ]
      );

      $client = new Client(['handler' => $mock]);
      return $client;
    }


    public function testGetResponseReasonPhrase() {
      $parser = new \Xparse\Parser\Parser($this->getDemoClient());
      $url = 'http://test.com/url/';
      $parser->get($url);
      $this->assertEquals('OK', $parser->getLastResponse()->getReasonPhrase());

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
