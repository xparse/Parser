<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  /**
   * Try to convert input encoding
   */
  class ToUtfConverter implements EncodingConverterInterface {

    /**
     * @var null|string[]
     */
    private $supportedEncodings;


    /**
     * @inheritdoc
     */
    public function convert(string $html, string $contentType = ''): string {
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
      if (in_array($encoding, $this->getSupportedEncodings(), true)) {
        $html = mb_convert_encoding($html, 'utf-8', $encoding);
      }

      return $html;
    }


    /**
     * @return array
     */
    private function getSupportedEncodings(): array {
      if ($this->supportedEncodings === null) {
        $this->supportedEncodings = [];
        $findAliases = function_exists('mb_encoding_aliases');
        foreach (mb_list_encodings() as $encoding) {
          $encoding = strtolower($encoding);
          if ($encoding !== 'utf-8' and $encoding !== 'utf8') {
            $this->supportedEncodings[] = $encoding;
            if ($findAliases) {
              foreach (mb_encoding_aliases($encoding) as $encodingAlias) {
                $encodingAlias = strtolower($encodingAlias);
                $this->supportedEncodings[] = $encodingAlias;
              }
            }
          }
        }
      }
      return $this->supportedEncodings;
    }

  }