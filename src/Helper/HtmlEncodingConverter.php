<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  /**
   * Try to convert input encoding
   */
  class HtmlEncodingConverter implements EncodingConverterInterface {

    /**
     * @var array
     */
    private $supportedEncodings;


    public function __construct() {
      $this->supportedEncodings = $this->getSupportedEncodings();
    }

    /**
     * @inheritdoc
     */
    public function convertToUtf(string $html, string $contentType = '') : string {
      $encoding = null;
      if ($contentType !== '') {
        preg_match('!^.*charset=([A-Za-z0-9-]{4,})$!', $contentType, $contentTypeData);
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
      if (in_array($encoding, $this->supportedEncodings, true)) {
        $html = mb_convert_encoding($html, 'utf-8', $encoding);
      }

      return $html;
    }


    /**
     * @return array
     */
    private function getSupportedEncodings() : array {

      $hasAliasesFunction = function_exists('mb_encoding_aliases');
      $supportedEncodings = [];
      foreach (mb_list_encodings() as $encoding) {
        $encoding = strtolower($encoding);
        if ($encoding === 'utf-8' or $encoding === 'utf8') {
          continue;
        }

        $supportedEncodings[] = $encoding;
        if ($hasAliasesFunction) {
          foreach (mb_encoding_aliases($encoding) as $encodingAlias) {
            $encodingAlias = strtolower($encodingAlias);
            $supportedEncodings[] = $encodingAlias;
          }
        }
      }

      return $supportedEncodings;
    }

  }