<?php

  namespace Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Uri;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class LinkConverter {

    /**
     * Convert relative links, images scr and form actions to absolute
     *
     * @param ElementFinder $page
     * @param string $affectedUrl
     */
    public static function convertUrlsToAbsolute(ElementFinder $page, $affectedUrl) {

      $affected = new Uri($affectedUrl);

      $srcElements = $page->element('//*[@src] | //*[@href] | //form[@action]');
      $baseUrl = $page->value('//base/@href')->getFirst();

      foreach ($srcElements as $element) {
        $attributeName = 'href';

        if ($element->hasAttribute('action') === true and $element->tagName === 'form') {
          $attributeName = 'action';
        } else if ($element->hasAttribute('src') === true) {
          $attributeName = 'src';
        }

        $relative = $element->getAttribute($attributeName);

        # don`t change javascript in href
        if (preg_match('!^\s*javascript\s*:\s*!', $relative)) {
          continue;
        }

        if (parse_url($relative) === false) {
          continue;
        }

        if (!empty($baseUrl) and !preg_match('!^(/|http)!i', $relative)) {
          $relative = Uri::resolve(new Uri($baseUrl), $relative);
        }

        $url = Uri::resolve($affected, (string) $relative);
        $element->setAttribute($attributeName, (string) $url);
      }

    }

  }