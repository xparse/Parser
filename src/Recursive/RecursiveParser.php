<?php

declare(strict_types=1);

namespace Xparse\Parser\Recursive;

use Exception;
use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use PhpParser\Node\Expr\Array_;
use Xparse\ElementFinder\ElementFinderInterface;
use Xparse\Parser\ParserInterface;

class RecursiveParser implements IteratorAggregate
{
    /**
     * @var array<string>
     */
    private array $links = [];

    /**
     * @var ParserInterface
     */
    private ParserInterface $parser;

    /**
     * @var string[]
     */
    private array $expressions = [];


    /**
     * @param ParserInterface $parser
     * @param string[] $expressions
     * @param string[] $links
     * @throws InvalidArgumentException
     */
    public function __construct(ParserInterface $parser, array $expressions, array $links = [])
    {
        $this->parser = $parser;
        $this->expressions = $expressions;
        $this->links = $links;
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
