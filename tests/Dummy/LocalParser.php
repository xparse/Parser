<?php

declare(strict_types=1);

namespace Test\Xparse\Parser\Dummy;

use Xparse\ElementFinder\ElementFinder;
use Xparse\ElementFinder\ElementFinderInterface;
use Xparse\Parser\ParserInterface;

class LocalParser implements ParserInterface
{

    private ?ElementFinderInterface $last = null;
    /**
     * @var callable
     */
    private $logger;

    public function __construct(?callable $logger = null)
    {
        $this->logger = $logger ?: function ($url): void {
        };
    }

    final public function get(string $url): ElementFinderInterface
    {
        call_user_func($this->logger, $url);
        $this->last = new ElementFinder(file_get_contents(__DIR__ . '/' . $url));
        return $this->last;
    }

    final public function post(string $url, array $options): ElementFinderInterface
    {
        call_user_func($this->logger, $url);
        $this->last = new ElementFinder(file_get_contents(__DIR__ . '/' . $url));
        return $this->last;
    }

    final public function getLastPage(): ?ElementFinderInterface
    {
        return $this->last;
    }
}
