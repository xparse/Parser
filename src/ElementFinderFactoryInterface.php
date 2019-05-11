<?php

declare(strict_types=1);

namespace Xparse\Parser;

use Psr\Http\Message\ResponseInterface;
use Xparse\ElementFinder\ElementFinder;
use Xparse\ElementFinder\ElementFinderInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
interface ElementFinderFactoryInterface
{

    public function create(ResponseInterface $response, string $affectedUrl = ''): ElementFinderInterface;

}