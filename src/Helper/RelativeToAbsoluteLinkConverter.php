<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Uri;
  use GuzzleHttp\Psr7\UriResolver;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class RelativeToAbsoluteLinkConverter implements LinkConverterInterface {

    /**
     * @inheritdoc
     */
    public function convert(ElementFinder $finder, string $affectedUrl = '') {

      $affected = new Uri($affectedUrl);

      $srcElements = $finder->element('//*[@src] | //*[@href] | //form[@action]');
      $baseUrl = $finder->value('//base/@href')->getFirst();

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

        if ($baseUrl !== null and !preg_match('!^(/|http)!i', $relative)) {
          $relative = UriResolver::resolve(new Uri($baseUrl), new Uri($relative));
        }

        $url = UriResolver::resolve($affected, new Uri($relative));
        $element->setAttribute($attributeName, (string) $url);
      }

    }

  }