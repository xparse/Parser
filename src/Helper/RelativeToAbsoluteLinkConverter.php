<?php

  declare(strict_types=1);

  namespace Xparse\Parser\Helper;

  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\ElementFinderInterface;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class RelativeToAbsoluteLinkConverter implements LinkConverterInterface {

    /**
     * @inheritdoc
     */
    public function convert(ElementFinder $finder, string $affectedUrl = ''): ElementFinderInterface {
      $modifier = new ConvertUrlElementFinderModifier(
        $affectedUrl,
        $finder->value('//base/@href')->first() ?? ''
      );
      return $finder->modify('//*[@src] | //*[@href] | //form[@action]', $modifier);
    }

  }