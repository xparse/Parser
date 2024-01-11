<?php

declare(strict_types=1);

namespace Test\Xparse\Parser\Helper;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\ElementFinder;
use Xparse\Parser\Helper\RelativeToAbsoluteLinkConverter;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class LinkConverterTest extends TestCase
{
    public function getConvertRelativeUrlToAbsolute(): array
    {
        return [
            [
                'html' => '<a href="/tt">a</a>',
                'expect' => '<a href="http://funivan.com/tt">a</a>',
                'url' => 'http://funivan.com/userName',
            ],
            [
                'html' => '<a href="javascript:custom()">a</a>',
                'expect' => '<a href="javascript:custom()">a</a>',
                'url' => 'http://funivan.com/userName',
            ],
            [
                'html' => '<a href="http:///example.com">a</a>',
                'expect' => '<a href="http:///example.com">a</a>',
                'url' => 'http://funivan.com/userName',
            ],
            [
                'html' => '<img src="/hello.jpg">',
                'expect' => '<img src="http://funivan.com/hello.jpg">',
                'url' => 'http://funivan.com/userName',
            ],
            [
                'html' => '<img src="hello.jpg">',
                'expect' => '<img src="http://funivan.com/userName/hello.jpg">',
                'url' => 'http://funivan.com/userName/',
            ],
            [
                'html' => '<form action="contacts.html"><a>1</a></form>',
                'expect' => '<form action="http://funivan.com/contacts.html"><a>1</a></form>',
                'url' => 'http://funivan.com/index.html',
            ],
            [
                'html' => '<form action="contacts.html"><a>1</a1></form>',
                'expect' => '<form action="http://funivan.com/contacts.html"><a>1</a></form>',
                'url' => 'http://funivan.com/index.html?user=123',
            ],
            [
                'html' => '<div><a href="../../contacts.html">1</a></div>',
                'expect' => '<div><a href="http://funivan.com/contacts.html">1</a></div>',
                'url' => 'http://funivan.com/user/dashboard',
            ],
            [
                'html' => '<div><a href="../../contact me">1</a></div>',
                'expect' => '<div><a href="http://funivan.com/contact%20me">1</a></div>',
                'url' => 'http://funivan.com/user/dashboard',
            ],
            [
                'html' => '<a href="search/?user=john&age=30">1</a>',
                'expect' => '<a href="http://funivan.com/search/?user=john&amp;age=30">1</a>',
                'url' => 'http://funivan.com/',
            ],
            [
                'html' => '<head><base href="http://www.example.com/images/" target="_blank"></head>
                     <body><img src="stickman.gif" width="24" height="39" alt="Stickman"></body>',
                'expect' => '<img src="http://www.example.com/images/stickman.gif" width="24" height="39" alt="Stickman">',
                'url' => 'http://www.example.com/',
            ],
            [
                'html' => '<head><base href="http://www.example.com/data/images/" target="_blank"></head>
                     <body><img src="../stickman.gif" width="24" height="39" alt="Stickman"></body>',
                'expect' => '<img src="http://www.example.com/data/stickman.gif" width="24" height="39" alt="Stickman">',
                'url' => 'http://www.example.com/',
            ],
            [
                'html' => '<head><base href="http://www.example.com/images/" target="_blank"></head>
                     <body><a href="http://www.w3schools.com">W3Schools</a></body>',
                'expect' => '<a href="http://www.w3schools.com">W3Schools</a>',
                'url' => 'http://www.example.com/',
            ],
        ];
    }

    /**
     * @dataProvider getConvertRelativeUrlToAbsolute
     */
    public function testConvertRelativeUrlToAbsolute(string $html, string $expect, string $url): void
    {
        $page = new ElementFinder($html);

        $page = (new RelativeToAbsoluteLinkConverter())->convert($page, $url);

        $body = $page->content('//body')->first();
        self::assertEquals($expect, $body);
    }
}
