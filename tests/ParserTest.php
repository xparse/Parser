<?php

  namespace Xparse\Parser\Test;

  use GuzzleHttp\Client;
  use GuzzleHttp\Ring\Client\MockHandler;

  /**
   *
   * @package Xparse\Parser\Test
   */
  class ParserTest extends \PHPUnit_Framework_TestCase {

    public function testInit() {

      $parser = new \Xparse\Parser\Parser();
      $this->assertEquals(new Client(), $parser->getClient());
    }

    public function testGet() {

      $client = $this->getDemoClinet();
      $parser = new \Xparse\Parser\Parser($client);

      $this->assertEquals($client, $parser->getClient());

      $page = $parser->get('http://test.com');

      $this->assertInstanceOf(get_class(new \Xparse\Parser\Page()), $page);
      $this->assertEquals($page, $parser->getLastPage());
      $this->assertEquals($parser, $page->getParser());
      $this->assertEquals(null, $page->getEffectedUrl());

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
    protected function getDemoClinet() {
      $mock = new MockHandler(array(
        'status' => 200,
        'headers' => array(),
        'body' => $this->getHtmlData('/test-get.html')
      ));

      $client = new Client(['handler' => $mock]);
      return $client;
    }

  }
