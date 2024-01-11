<?php

declare(strict_types=1);

namespace Test\Xparse\Parser\Recursive;

use PHPUnit\Framework\TestCase;
use Test\Xparse\Parser\Dummy\LocalParser;
use Xparse\Parser\Recursive\RecursiveParser;

final class RecursiveParserTest extends TestCase
{
    public function testAllLinks(): void
    {
        $links = [];
        $logger = function ($link) use (&$links): void {
            $links[] = $link;
        };
        $pages = new RecursiveParser(
            new LocalParser($logger),
            ["//div[@id='main']/a/@href", "//div[@id='a2']/a/@href"],
            ['a.html']
        );
        $h1 = [];
        foreach ($pages as $page) {
            $h1[] = $page->content('//h1')->replace('!\s!', '')->first();
        }
        self::assertSame(
            [
                'a.html', 'b.html', 'c.html', 'd.html'],
            $links
        );
        self::assertSame(
            ['a', 'b', 'c', 'd'],
            $h1
        );
    }
}
