<?php

declare(strict_types=1);

namespace Xparse\Parser;

use Xparse\ElementFinder\ElementFinderInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
interface ParserInterface
{
    public function get(string $url): ElementFinderInterface;

    public function post(string $url, array $options): ElementFinderInterface;

    public function getLastPage(): ?ElementFinderInterface;
}
