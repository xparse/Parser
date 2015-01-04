<?php

  namespace Xparse\Parser\Test;

  use GuzzleHttp\Ring\Client\MockHandler;

  /**
   *
   * @package Xparse\Parser\Test
   */
  class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testInit() {

      $parser = new \Xparse\Parser\Parser();
      $this->assertEquals(new \GuzzleHttp\Client(), $parser->getClient());
    }

    public function testGet() {

      $mock = new MockHandler(array(
        'status' => 200,
        'headers' => array(),
        'body' => $this->getHtmlData('/test-get.html')
      ));

      $client = new \GuzzleHttp\Client(['handler' => $mock]);
      $parser = new \Xparse\Parser\Parser($client);

      $this->assertEquals($client, $parser->getClient());

      $page = $parser->get('http://test.com');

      $this->assertInstanceOf(get_class(new \Xparse\Parser\Page()), $page);
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

  }
