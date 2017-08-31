<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  use Xparse\ElementFinder\ElementFinder;

  /**
   * Convert relative paths to absolute
   */
  interface LinkConverterInterface {

    /**
     * Convert relative links, images src and form actions to absolute
     *
     * @param ElementFinder $finder
     * @param string $affectedUrl
     */
    public function relativeToAbsolute(ElementFinder $finder, string $affectedUrl = '') : void;

  }