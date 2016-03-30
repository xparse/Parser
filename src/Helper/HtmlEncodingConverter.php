<?php

  namespace Xparse\Parser\Helper;

  /**
   * Class EncodingConverter
   * @package Xparse\Parser\Helper
   */
  class HtmlEncodingConverter {

    /**
     * @var array|null
     */
    private static $supportedEncodings = null;


    /**
     * Try to detect input encoding from contentType or from html <meta> tag
     *
     * @param string $html
     * @param bool $contentType
     * @return string
     */
    public static function convertToUtf($html, $contentType = false) {
      if (!empty($contentType)) {
        preg_match("!^.*charset=([A-Za-z0-9-]{4,})$!", $contentType, $contentTypeData);
        $encoding = !empty($contentTypeData[1]) ? strtoupper(trim($contentTypeData[1])) : '';
      }

      if (empty($encoding)) {
        preg_match("!.*<meta.*charset=[\"']?[ \t]*([A-Za-z0-9-]{4,})[ \t]*[\"']!mi", $html, $metaContentType);
        $encoding = !empty($metaContentType[1]) ? strtoupper(trim($metaContentType[1])) : '';
      }

      $supportedEncodings = self::getSupportedEncodings();
      if (in_array($encoding, $supportedEncodings)) {
        $html = mb_convert_encoding($html, 'UTF-8', $encoding);
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