<?php

  namespace Xparse\Parser;

  use GuzzleHttp\ClientInterface;
  use GuzzleHttp\HandlerStack;
  use GuzzleHttp\Psr7\Request;
  use Psr\Http\Message\RequestInterface;
  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\Helper;

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
     * Set true if we need automatically convert encoding to utf-8
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
     * @var null|ResponseInterface
     */
    protected $lastResponse = null;

    /**
     *
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null) {
      if (empty($client)) {
        $client = new \GuzzleHttp\Client(array(
          \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true
        ));

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

      $request = new \GuzzleHttp\Psr7\Request('GET', $url);
      return $this->send($request);
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

      $request = new Request('POST', $url, array(
        'body' => $data
      ));

      return $this->send($request);
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
     * @return null|ResponseInterface
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

    /**
     * @param $request
     * @param array $options
     * @return Page
     * @throws \Exception
     */
    public function send(RequestInterface $request, $options = array()) {
      /** @var RequestInterface $lastRequest */
      $lastRequest = null;


      /** @var HandlerStack $handler */
      $stack = $this->client->getConfig('handler');

      if (!empty($stack) and $stack instanceof HandlerStack) {
        $stack->remove('last_request');
        $stack->push(\GuzzleHttp\Middleware::mapRequest(function (RequestInterface $request) use (&$lastRequest) {
          $lastRequest = $request;
          return $request;
        }), 'last_request');

      }
      $response = $this->client->send($request, $options);


      $htmlCode = Helper::safeEncodeStr((string) $response->getBody());
      $htmlCode = mb_convert_encoding($htmlCode, 'HTML-ENTITIES', "UTF-8");

      //@todo convert encoding

      $page = new Page((string) $htmlCode);
      $page->setParser($this);
      if (!empty($lastRequest)) {
        $page->setEffectedUrl($lastRequest->getUri()->__toString());
      } else {
        $page->setEffectedUrl($request->getUri()->__toString());
      }

      if ($this->convertRelativeLinksState) {
        $page->convertRelativeLinks();
      }


      $this->setLastPage($page);
      $this->lastResponse = $response;
      return $page;
    }

  }
