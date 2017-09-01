<?php

  declare(strict_types=1);

  namespace Xparse\Parser;

  use Xparse\ElementFinder\ElementFinder;

  /**
   *
   */
  interface ParserInterface {

    /**
     * @param string $url
     * @return ElementFinder
     */
    public function get(string $url) : ElementFinder;

    /**
     * @param string $url
     * @param array $options
     * @return ElementFinder
     */
    public function post(string $url, array $options) : ElementFinder;

    /**
     * @return ElementFinder|null
     */
    public function getLastPage();

  }
