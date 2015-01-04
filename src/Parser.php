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
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function get($url) {
      $html = $this->client->get($url)->getBody();

      $page = $this->createPage($html);
      $this->setLastPage($page);

      return $page;
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

      $page = $this->createPage($html);
      $this->setLastPage($page);
      return $page;
    }


    /**
     * @param string $html
     * @return Page
     */
    public function createPage($html) {
      $page = new Page($html);

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

    /**
     * @return ClientInterface
     */
    public function getClient() {
      return $this->client;
    }

  }
