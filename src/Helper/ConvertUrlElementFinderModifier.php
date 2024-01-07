<?php

declare(strict_types=1);

namespace Xparse\Parser\Helper;

use DOMElement;
use DOMNodeList;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Xparse\ElementFinder\DomNodeListAction\DomNodeListActionInterface;

/**
 * @internal
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ConvertUrlElementFinderModifier implements DomNodeListActionInterface
{

    public function __construct(private string $affectedUrl, private string $baseUrl)
    {
    }


    final public function execute(DOMNodeList $nodeList): void
    {
        $affected = new Uri($this->affectedUrl);
        foreach ($nodeList as $element) {
            assert($element instanceof DOMElement);
            $attribute = $this->attributeName($element);
            $relative = $element->getAttribute($attribute);
            $isValid = parse_url($relative) !== false;
            if (
                $isValid
                &&
                !preg_match('!^\s*javascript\s*:\s*!', $relative)
            ) {
                if ($this->baseUrl !== '' && !preg_match('!^(/|http)!i', $relative)) {
                    $relative = UriResolver::resolve(new Uri($this->baseUrl), new Uri($relative))->__toString();
                }
                $url = UriResolver::resolve($affected, new Uri($relative))->__toString();
                $element->setAttribute($attribute, $url);
            }
        }
    }


    private function attributeName(DOMElement $element): string
    {
        $name = 'href';
        if ($element->tagName === 'form' && $element->hasAttribute('action') === true) {
            $name = 'action';
        } else if ($element->hasAttribute('src') === true) {
            $name = 'src';
        }
        return $name;
    }

}