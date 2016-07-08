<?php

  namespace Xparse\Parser;

  use GuzzleHttp\Client;
  use GuzzleHttp\ClientInterface;
  use GuzzleHttp\Psr7\Request;
  use GuzzleHttp\RequestOptions;
  use GuzzleHttp\TransferStats;
  use Psr\Http\Message\RequestInterface;
  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ParserInterface\ParserInterface;

  /**
   *
   * @package Xparse\Parser
   */
  class Parser implements ParserInterface {

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
     * @var ElementFinderFactoryInterface
     */
    protected $elementFinderFactory = null;

    /**
     * @var null|ResponseInterface
     */
    protected $lastResponse = null;


    /**
     * @param ClientInterface|null $client
     * @param ElementFinderFactoryInterface|null $elementFinderFactory
     */
    public function __construct(ClientInterface $client = null, ElementFinderFactoryInterface $elementFinderFactory = null) {
      if ($client === null) {
        $client = new Client([
          RequestOptions::ALLOW_REDIRECTS => true,
          RequestOptions::COOKIES => new \GuzzleHttp\Cookie\CookieJar(),
        ]);
      }

      if ($elementFinderFactory === null) {
        $elementFinderFactory = new ElementFinderFactory();
      }

      $this->elementFinderFactory = $elementFinderFactory;

      $this->client = $client;
    }


    /**
     * @param string $url
     * @param array $options
     * @return ElementFinder
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function get($url, array $options = []) {
      if (empty($url) or !is_string($url)) {
        throw new \InvalidArgumentException('Url must be not empty and string.');
      }

      $request = new Request('GET', $url);
      return $this->send($request, $options);
    }


    /**
     * @param string $url
     * @param string|resource|\Psr\Http\Message\StreamInterface $body Message body.
     * @param array $options
     * @return ElementFinder
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function post($url, $body = null, array $options = []) {

      if (empty($url) or !is_string($url)) {
        throw new \InvalidArgumentException('Url must be not empty and string.');
      }

      $request = new Request('POST', $url, [], $body);

      return $this->send($request, $options);
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

      $prevCallback = !empty($options['on_stats']) ? $options['on_stats'] : null;

      $effectiveUri = null;
      $options['on_stats'] = function (TransferStats $stats) use (&$effectiveUri, $prevCallback) {
        $effectiveUri = $stats->getEffectiveUri();
        if ($prevCallback !== null) {
          /** @var callable $prevCallback */
          $prevCallback($stats);
        }
      };

      $response = $this->client->send($request, $options);

      $guzzleEffectiveUrl = $response->getHeaderLine('X-GUZZLE-EFFECTIVE-URL');
      if (!empty($guzzleEffectiveUrl)) {
        $effectiveUri = $guzzleEffectiveUrl;
      }

      $page = $this->elementFinderFactory->create($response, $effectiveUri);

      $this->setLastPage($page);
      $this->lastResponse = $response;
      return $page;
    }


    /**
     * @return ElementFinderFactoryInterface
     */
    public function getElementFinderFactory() {
      return $this->elementFinderFactory;
    }

  }
