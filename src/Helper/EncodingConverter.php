<?php

namespace Xparse\Parser\Helper;

use GuzzleHttp\Psr7\Response;

/**
 * Class EncodingConverter
 * @package Xparse\Parser\Helper
 */
class EncodingConverter
{

  /**
   * @var array|null
   */
  private static $supportedEncodings = null;

  /**
   * @param Response $response
   * @param $html
   * @param $convertToEncoding
   * @return mixed
   */
  public static function convertTo(Response $response, $html, $convertToEncoding) {
    $contentType = $response->getHeaderLine('content-type');

    if (!empty($contentType)) {
      preg_match("!^.*charset=([A-Za-z0-9-]{4,})$!", $contentType, $contentTypeData);
      $encoding = !empty($contentTypeData[1]) ? strtoupper(trim($contentTypeData[1])) : '';
    }

    if (empty($encoding)){
      preg_match("!.*<meta.*charset=[\"']?[ \t]*([A-Za-z0-9-]{4,})[ \t]*[\"']!mi", $html, $metaContentType);
      $encoding = !empty($metaContentType[1]) ? strtoupper(trim($metaContentType[1])) : '';
    }

    $supportedEncodings = self::getSupportedEncodings();
    if (in_array($encoding, $supportedEncodings)) {
      $html = mb_convert_encoding($html, $convertToEncoding, $encoding);
    }

    return $html;
  }


  /**
   * @return array
   */
  private static function getSupportedEncodings() {
    if (self::$supportedEncodings !== null) {
      return self::$supportedEncodings;
    }

    self::$supportedEncodings = [];
    foreach (mb_list_encodings() as $encoding) {
      if ($encoding == 'UTF-8' or $encoding == 'UTF8') {
        continue;
      }

      self::$supportedEncodings[] = $encoding;
      self::$supportedEncodings = array_merge(self::$supportedEncodings, mb_encoding_aliases($encoding));
    }

    return self::$supportedEncodings;
  }
}