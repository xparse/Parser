<?php

declare(strict_types=1);

namespace Xparse\Parser\Helper;

use Exception;
use Xparse\ElementFinder\ElementFinderInterface;

/**
 * Convert relative paths to absolute
 *
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
interface LinkConverterInterface
{

    /**
     * Convert relative links, images src and form actions to absolute
     *
     * @throws Exception
     */
    public function convert(ElementFinderInterface $finder, string $affectedUrl = ''): ElementFinderInterface;

}