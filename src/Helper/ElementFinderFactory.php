<?php

  namespace Xparse\Parser\Helper;

  use Xparse\ElementFinder\Helper;
  use Xparse\ElementFinder\ElementFinder;

  class ElementFinderFactory
  {
    /**
     * @param $response
     * @param string $url
     * @return ElementFinder
     */
    public static function create($response, $url = '')
    {
      $html = $response->getBody();
      $html = Helper::safeEncodeStr((string)$html);

      $supportedEncodings = [];
      foreach (mb_list_encodings() as $encoding) {
        if ($encoding == 'UTF-8' || $encoding == 'UTF8') {
          continue;
        }

        $supportedEncodings[] = $encoding;
        $supportedEncodings = array_merge($supportedEncodings, mb_encoding_aliases($encoding));
      }

      $contentType = $response->getHeaderLine('content-type');

      if ($contentType) {
        preg_match("!^.*charset=([A-Za-z0-9-]{4,})$!", $contentType, $contentTypeData);
        $encoding = strtoupper(trim($contentTypeData[1]));
      } else {
        preg_match("!.*<meta.*charset=\"?([A-Za-z0-9-]{4,})\"!mi", $html, $metaContentType);
        $encoding = !empty($metaContentType[1]) ? strtoupper(trim($metaContentType[1])) : '';
      }

      if (in_array($encoding, $supportedEncodings)) {
        $html = mb_convert_encoding($html, 'UTF-8', $encoding);
      }

      $page = new ElementFinder((string)$html);

      if ($url) {
        LinkConverter::convertUrlsToAbsolute($page, $url);
      }

      return $page;
    }
  }