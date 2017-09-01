<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  /**
   * Try to convert input encoding
   */
  class ToUtfConverter implements EncodingConverterInterface {

    /**
     * @var array|null
     */
    private static $supportedEncodings;


    /**
     * @inheritdoc
     */
    public function convert(string $html, string $contentType = '') : string {
      $encoding = null;
      if (preg_match('!^.*charset=([A-Za-z0-9-]{4,})$!', $contentType, $contentTypeData) === 1) {
        $encoding = $contentTypeData[1];
      } elseif (preg_match("!.*<meta.*charset=[\"']?[ \t]*([A-Za-z0-9-]{4,})[ \t]*[\"']!mi", $html, $metaContentType) === 1) {
        $encoding = $metaContentType[1];
      }

      if ($encoding === null) {
        return $html;
      }

      $encoding = strtolower($encoding);
      if (in_array($encoding, self::getSupportedEncodings(), true)) {
        $html = mb_convert_encoding($html, 'utf-8', $encoding);
      }

      return $html;
    }


    /**
     * @return array
     */
    private static function getSupportedEncodings() : array {

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