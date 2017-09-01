<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  interface EncodingConverterInterface {

    /**
     * Try to detect input encoding from contentType or from html <meta> tag
     *
     * @param string $html
     * @param string $contentType
     * @return string
     */
    public function convert(string $html, string $contentType) : string;

  }