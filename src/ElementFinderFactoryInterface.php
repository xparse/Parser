<?php

  namespace Xparse\Parser;

  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @package Xparse\Parser
   */
  interface ElementFinderFactoryInterface {

    /**
     * @param ResponseInterface $response
     * @param string|null $affectedUrl
     * @return ElementFinder
     */
    public function create(ResponseInterface $response, $affectedUrl = null);

  }