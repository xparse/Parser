<?php

declare(strict_types=1);

namespace Xparse\Parser\Recursive;

use Exception;
use Generator;
use IteratorAggregate;
use Xparse\ElementFinder\ElementFinderInterface;
use Xparse\Parser\ParserInterface;

class RecursiveParser implements IteratorAggregate
{
    /**
     * @param string[] $expressions
     * @param string[] $links
     */
    public function __construct(
        private readonly ParserInterface $parser,
        private readonly array $expressions,
        private array $links = []
    ) {
    }

    /**
     * @return ElementFinderInterface[]|Generator
     * @throws Exception
     */
    final public function getIterator(): Generator
    {
        $left = $this->links;
        $done = [];
        while ($left !== []) {
            $left = array_unique(array_diff($left, $done));
            foreach ($left as $index => $link) {
                unset($left[$index]);
                $done[] = $link;
                $page = $this->parser->get($link);
                $nextLinks = [];
                foreach ($this->expressions as $expression) {
                    $nextLinks = array_merge($nextLinks, $page->value($expression)->all());
                }
                $left = array_merge($left, $nextLinks);
                yield $page;
            }
        }
    }
}
