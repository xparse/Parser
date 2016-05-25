<?php

  namespace Xparse\Parser;

  use GuzzleHttp\ClientInterface;
  use GuzzleHttp\Psr7\Request;
  use GuzzleHttp\TransferStats;
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
     * @param ElementFinderFactoryInterface|null $elementFinderFactory
     */
    public function __construct(ClientInterface $client = null, ElementFinderFactoryInterface $elementFinderFactory = null) {
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
      $page = $this->elementFinderFactory->create($response, $effectiveUri);

      $this->setLastPage($page);
      $this->lastResponse = $response;
      return $page;
    }

  }
