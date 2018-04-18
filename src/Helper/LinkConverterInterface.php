<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\ElementFinderInterface;

  /**
   * Convert relative paths to absolute
   */
  interface LinkConverterInterface {

    /**
     * Convert relative links, images src and form actions to absolute
     *
     * @param ElementFinder $finder
     * @param string $affectedUrl
     * @return ElementFinderInterface
     * @throws \Exception
     */
    public function convert(ElementFinder $finder, string $affectedUrl = ''): ElementFinderInterface;

  }