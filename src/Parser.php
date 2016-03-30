<?php

  namespace Xparse\Parser;

  use GuzzleHttp\ClientInterface;
  use GuzzleHttp\Handler\MockHandler;
  use GuzzleHttp\HandlerStack;
  use GuzzleHttp\Psr7\Request;
  use Psr\Http\Message\RequestInterface;
  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper;

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
     * @var ClientInterface
     */
    protected $client = null;

    /**
     * @var ElementFinderFactory
     */
    protected $elementFinderFactory = null;

    /**
     * @var null|ResponseInterface
     */
    protected $lastResponse = null;


    /**
     * @param ClientInterface|null $client
     * @param ElementFinderFactory|null $elementFinderFactory
     */
    public function __construct(ClientInterface $client = null, ElementFinderFactory $elementFinderFactory = null) {
      if (empty($client)) {
        $client = new \GuzzleHttp\Client([
          \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true,
        ]);

      }

      if ($elementFinderFactory === null) {
        $this->elementFinderFactory = new ElementFinderFactory();
      } else {
        $this->elementFinderFactory = $elementFinderFactory;
      }

      $this->client = $client;
    }


    /**
     * @param string $url
     * @return \Xparse\ElementFinder\ElementFinder
     * @throws \InvalidArgumentException
     */
    public function get($url) {
      if (empty($url) or !is_string($url)) {
        throw new \InvalidArgumentException('Url must be not empty and string.');
      }

      $request = new \GuzzleHttp\Psr7\Request('GET', $url);
      return $this->send($request);
    }


    /**
     * @param string $url
     * @param array $data
     * @return \Xparse\ElementFinder\ElementFinder
     * @throws \InvalidArgumentException
     */
    public function post($url, $data) {

      if (empty($url) or !is_string($url)) {
        throw new \InvalidArgumentException('Url must be not empty and string.');
      }

      $request = new Request('POST', $url, [
        'body' => $data,
      ]);

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
     * @param $request
     * @param array $options
     * @return \Xparse\ElementFinder\ElementFinder
     * @throws \Exception
     */
    public function send(RequestInterface $request, array $options = []) {
      /** @var RequestInterface $lastRequest */
      $lastRequest = null;


      /** @var HandlerStack $handler */
      $stack = $this->client->getConfig('handler');

      if (!empty($stack) and ($stack instanceof HandlerStack)) {
        $stack->remove('last_request');
        $stack->push(\GuzzleHttp\Middleware::mapRequest(function (RequestInterface $request) use (&$lastRequest) {
          $lastRequest = $request;
          return $request;
        }), 'last_request');

      }
      $response = $this->client->send($request, $options);

      $url = (!empty($lastRequest)) ? $lastRequest->getUri()->__toString() : '';

      $page = $this->elementFinderFactory->create($response, $url);

      $this->setLastPage($page);
      $this->lastResponse = $response;
      return $page;
    }

  }
