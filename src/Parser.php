<?php

  namespace Xparse\Parser;

  use GuzzleHttp\ClientInterface;

  /**
   *
   * @package Xparse\Parser
   */
  class Parser implements \Xparse\ParserInterface\ParserInterface {

    /**
     * Set true if we need to automaticaly convert reletive links to absolute
     */
    protected $convertRelativeLinksState = true;

    /**
     * Set true if we need automaticaly convert encoding to utf-8
     */
    protected $convertEncodingState = true;

    /**
     *
     * @var null|\Xparse\ElementFinder\ElementFinder
     */
    protected $lastPage = null;

    /**
     * @var ClientInterface
     */
    protected $client = null;

    /**
     * @var \GuzzleHttp\Message\ResponseInterface
     */
    protected $lastResponse = null;

    /**
     *
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null) {
      if (empty($client)) {
        $client = new \GuzzleHttp\Client();
      }

      $this->client = $client;
    }

    /**
     * @param string $url
     * @return Page
     */
    public function get($url) {
      if (empty($url) or !is_string($url)) {
        throw new \InvalidArgumentException("Url must be not empty and string.");
      }
      $response = $this->client->get($url);

      $page = $this->createPage($response->getBody());
      $page->setEffectedUrl($response->getEffectiveUrl());

      $this->setLastPage($page);
      $this->lastResponse = $response;

      return $page;
    }

    /**
     * @param string $url
     * @param array $data
     * @return Page
     */
    public function post($url, $data) {
      
      if (empty($url) or !is_string($url)) {
        throw new \InvalidArgumentException("Url must be not empty and string.");
      }
      
      $response = $this->client->post($url, array(
        'body' => $data
      ));

      $page = $this->createPage($response->getBody());
      $page->setEffectedUrl($response->getEffectiveUrl());

      $this->setLastPage($page);
      $this->lastResponse = $response;

      return $page;
    }


    /**
     * @param string $html
     * @return Page
     */
    public function createPage($html) {
      $page = new Page($html);
      $page->setParser($this);

      //@todo convert links to absolute
      //@todo convert encoding

      return $page;
    }

    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getLastPage() {
      return $this->lastPage;
    }

    /**
     * @param \Xparse\ElementFinder\ElementFinder $lastPage
     * @return $this
     */
    public function setLastPage($lastPage) {
      $this->lastPage = $lastPage;
      return $this;
    }

    /**
     * @return \Guzzle\Http\Message\Response
     */
    public function getLastResponse() {
      return $this->lastResponse;
    }


    /**
     * @return ClientInterface
     */
    public function getClient() {
      return $this->client;
    }

    /**
     *
     * @return boolean
     */
    public function getConvertRelativeLinksState() {
      return $this->convertRelativeLinksState;
    }

    /**
     * @param boolean $convertRelativeLinksState
     * @return $this
     */
    public function setConvertRelativeLinksState($convertRelativeLinksState) {
      $this->convertRelativeLinksState = $convertRelativeLinksState;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getConvertEncodingState() {
      return $this->convertEncodingState;
    }

    /**
     * @param boolean $convertEncodingState
     * @return $this
     */
    public function setConvertEncodingState($convertEncodingState) {
      $this->convertEncodingState = $convertEncodingState;
      return $this;
    }

  }
