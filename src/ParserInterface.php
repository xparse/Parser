<?php

declare(strict_types=1);

namespace Xparse\Parser;

use Xparse\ElementFinder\ElementFinder;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
interface ParserInterface
{

    /**
     */
    public function get(string $url): ElementFinder;

    /**
     */
    public function post(string $url, array $options): ElementFinder;

    /**
     * @return ElementFinder|null
     */
    public function getLastPage();

}
