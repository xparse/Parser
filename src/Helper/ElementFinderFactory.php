<?php

  namespace Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper;

  /**
   * Create ElementFinder.
   * Convert charset to UTF-8
   * Convert relative links to absolute
   */
  class ElementFinderFactory {

    /**
     * @var array|null
     */
    private static $supportedEncodings = null;


    /**
     * @param Response $response
     * @param string $affectedUrl
     * @return ElementFinder
     */
    public static function create(Response $response, $affectedUrl = '') {
      $html = $response->getBody();
      $html = Helper::safeEncodeStr((string) $html);

      $supportedEncodings = self::getSupportedEncodings();

      $contentType = $response->getHeaderLine('content-type');

      if (!empty($contentType)) {
        preg_match("!^.*charset=([A-Za-z0-9-]{4,})$!", $contentType, $contentTypeData);
        $encoding = !empty($contentTypeData[1]) ? strtoupper(trim($contentTypeData[1])) : '';
      }

      if (empty($encoding)){
        preg_match("!.*<meta.*charset=[\"']?[ \t]*([A-Za-z0-9-]{4,})[ \t]*[\"']!mi", $html, $metaContentType);
        $encoding = !empty($metaContentType[1]) ? strtoupper(trim($metaContentType[1])) : '';
      }
      
      if (in_array($encoding, $supportedEncodings)) {
        $html = mb_convert_encoding($html, 'UTF-8', $encoding);
      }

      $page = new ElementFinder((string) $html);

      if ($affectedUrl) {
        LinkConverter::convertUrlsToAbsolute($page, $affectedUrl);
      }

      return $page;
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