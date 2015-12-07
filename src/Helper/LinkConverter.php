<?php

  namespace Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Uri;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class LinkConverter {

    /**
     * Modify elements in page
     *
     * Convert relative links to absolute
     *
     * @param ElementFinder $page
     * @param string $affectedUrl
     */
    public static function convertUrlsToAbsolute(ElementFinder $page, $affectedUrl) {

      $affected = new Uri($affectedUrl);

      $srcElements = $page->elements('//*[@src] | //*[@href] | //form[@action]');
      foreach ($srcElements as $element) {
        if ($element->hasAttribute('action') == true and $element->tagName == 'form') {
          $attrName = 'action';
        } else if ($element->hasAttribute('src') == true) {
          $attrName = 'src';
        } else {
          $attrName = 'href';
        }

        $relative = $element->getAttribute($attrName);
        # don`t change javascript in href
        if (preg_match('!^\s*javascript\s*:\s*!', $relative)) {
          continue;
        }

        if (parse_url($relative) === false) {
          continue;
        }

        $baseUrl = $page->attribute('//base/@href')->getFirst();
        if (!empty($baseUrl) and !preg_match("!^(/|http)!i", $relative)) {
          $relative = Uri::resolve(new Uri($baseUrl), $relative);
        }

        $url = Uri::resolve($affected, (string) $relative);
        $element->setAttribute($attrName, (string) $url);
      }

    }

  }