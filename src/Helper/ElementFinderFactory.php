<?php

  namespace Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper;

  /**
   * Create ElementFinder.
   * Convert charset to UTF-8
   * Convert relative links to absolute
   * @package Xparse\Parser\Helper
   */
  class ElementFinderFactory implements ElementFinderFactoryInterface {

    /**
     * @param Response $response
     * @param string $affectedUrl
     * @return ElementFinder
     */
    public function create(Response $response, $affectedUrl = '') {
      $html = $response->getBody();
      $html = Helper::safeEncodeStr((string) $html);
      $html = EncodingConverter::convertTo($response, $html, 'UTF-8');
      $page = new ElementFinder((string) $html);

      if ($affectedUrl) {
        LinkConverter::convertUrlsToAbsolute($page, $affectedUrl);
      }

      return $page;
    }

  }