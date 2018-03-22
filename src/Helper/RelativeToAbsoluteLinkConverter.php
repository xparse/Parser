<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Uri;
  use GuzzleHttp\Psr7\UriResolver;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper\NodeHelper;
  use Xparse\ElementFinder\Helper\StringHelper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class RelativeToAbsoluteLinkConverter implements LinkConverterInterface {

    /**
     * @inheritdoc
     */
    public function convert(ElementFinder $finder, string $affectedUrl = ''): ElementFinder {
      $affected = new Uri($affectedUrl);
      $dom = new \DomDocument();
      $internalErrors = libxml_use_internal_errors(true);
      $disableEntities = libxml_disable_entity_loader();
      $data = mb_convert_encoding(StringHelper::safeEncodeStr((string) $finder->content('.')->first()), 'HTML-ENTITIES', 'UTF-8');
      $dom->loadHTML($data, LIBXML_NOCDATA & LIBXML_NOERROR);
      libxml_clear_errors();
      libxml_use_internal_errors($internalErrors);
      libxml_disable_entity_loader($disableEntities);
      $xpath = new \DomXPath($dom);

      $srcElements = $xpath->query('//*[@src] | //*[@href] | //form[@action]');
      $baseUrlAttr = $xpath->query('//base/@href')->item(0) ?? new \DOMAttr('href', '');
      $baseUrl = $baseUrlAttr->value;

      foreach ($srcElements as $element) {
        /** @var \DOMElement $element */
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

        if ($baseUrl !== '' and !preg_match('!^(/|http)!i', $relative)) {
          $relative = UriResolver::resolve(new Uri($baseUrl), new Uri($relative));
        }

        $url = UriResolver::resolve($affected, new Uri($relative));
        $element->setAttribute($attributeName, (string) $url);
      }
      $output = NodeHelper::getInnerContent(
        $xpath->query('.')->item(0) ?? new \DOMNode()
      );
      return new ElementFinder(
        $output
      );
    }

  }