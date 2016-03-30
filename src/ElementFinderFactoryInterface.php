<?php

  namespace Xparse\Parser;

  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @package Xparse\Parser
   */
  interface ElementFinderFactoryInterface {

    /**
     * @param Response $response
     * @param string $affectedUrl
     * @return ElementFinder
     */
    public function create(Response $response, $affectedUrl = '');

  }