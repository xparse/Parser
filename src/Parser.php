<?php

  namespace Xparse\Parser;

  use GuzzleHttp\ClientInterface;

  /**
   *
   * @package Xparse\Parser
   */
  class Parser implements \Xparse\ParserInterface\ParserInterface {

    /**
     *
     * @var null|\Xparse\ElementFinder\ElementFinder
     */
    protected $lastPage = null;

    /**
     *
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null, \Xparse\ElementFinder\ElementFinder $elementFinder = null) {
      if (empty($client)) {
        $client = new \GuzzleHttp\Client();
      }

      $this->client = $client;
    }

    /**
     * @param string $url
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function get($url) {
      $html = $this->client->get($url)->getBody();
      $this->lastPage = $this->createPage($html);

      return $this->lastPage;
    }

    /**
     * @param string $url
     * @param array $data
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function post($url, $data) {
      $html = $this->client->post($url, array(
        'body' => $data
      ))->getBody();
      $this->lastPage = $this->createPage($html);

      return $this->lastPage;
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getLastPage() {

    }

    /**
     * @param $html
     * @return ParserElementFinder
     */
    protected function createPage($html) {
      $page = new ParserElementFinder($html);
      $page->convertLinksToAbsolute($this->client->getBaseUrl());
      return $page;
    }
  }
