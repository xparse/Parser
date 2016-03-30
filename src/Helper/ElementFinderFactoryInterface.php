<?php

namespace Xparse\Parser\Helper;
use GuzzleHttp\Psr7\Response;

/**
 * Interface ElementFinderFactoryInterface
 * @package Xparse\Parser\Helper
 */
interface ElementFinderFactoryInterface
{

  /**
   * @param Response $response
   * @param string $affectedUrl
   * @return mixed
   */
  public function create(Response $response, $affectedUrl = '');
}