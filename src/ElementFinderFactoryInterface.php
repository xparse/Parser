<?php

  declare(strict_types=1);

  namespace Xparse\Parser;

  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\ElementFinder;

  interface ElementFinderFactoryInterface {

    /**
     * @param ResponseInterface $response
     * @param string $affectedUrl
     * @return ElementFinder
     */
    public function create(ResponseInterface $response, string $affectedUrl = '') : ElementFinder;

  }