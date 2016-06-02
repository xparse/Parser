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
      $encoding = null;
      if (!empty($contentType)) {
        preg_match("!^.*charset=([A-Za-z0-9-]{4,})$!", $contentType, $contentTypeData);
        $encoding = !empty($contentTypeData[1]) ? trim($contentTypeData[1]) : null;
      }

      if ($encoding === null) {
        preg_match("!.*<meta.*charset=[\"']?[ \t]*([A-Za-z0-9-]{4,})[ \t]*[\"']!mi", $html, $metaContentType);
        $encoding = !empty($metaContentType[1]) ? trim($metaContentType[1]) : null;
      }

      if ($encoding === null) {
        return $html;
      }

      $encoding = strtolower($encoding);
      if (in_array($encoding, self::getSupportedEncodings())) {
        $html = mb_convert_encoding($html, 'utf-8', $encoding);
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

      $hasAliasesFunction = function_exists('mb_encoding_aliases');
      self::$supportedEncodings = [];
      foreach (mb_list_encodings() as $encoding) {
        $encoding = strtolower($encoding);
        if ($encoding === 'utf-8' or $encoding === 'utf8') {
          continue;
        }

        self::$supportedEncodings[] = $encoding;
        if ($hasAliasesFunction) {
          foreach (mb_encoding_aliases($encoding) as $encodingAlias) {
            $encodingAlias = strtolower($encodingAlias);
            self::$supportedEncodings[] = $encodingAlias;
          }
        }
      }

      return self::$supportedEncodings;
    }

  }