<?php

declare(strict_types=1);

namespace Xparse\Parser\Helper;

use Xparse\ElementFinder\ElementFinderInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class RelativeToAbsoluteLinkConverter implements LinkConverterInterface
{

    public function convert(ElementFinderInterface $finder, string $affectedUrl = ''): ElementFinderInterface
    {
        $modifier = new ConvertUrlElementFinderModifier(
            $affectedUrl,
            $finder->value('//base/@href')->first() ?? ''
        );
        return $finder->modify('//*[@src] | //*[@href] | //form[@action]', $modifier);
    }

}