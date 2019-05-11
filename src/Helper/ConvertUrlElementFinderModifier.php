<?php

declare(strict_types=1);

namespace Xparse\Parser\Helper;

use DOMNodeList;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Xparse\ElementFinder\ElementFinder\ElementFinderModifierInterface;

/**
 * @internal
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ConvertUrlElementFinderModifier implements ElementFinderModifierInterface
{

    /**
     * @var string
     */
    private $affectedUrl;

    /**
     * @var string
     */
    private $baseUrl;


    public function __construct(string $affectedUrl, string $baseUrl)
    {
        $this->affectedUrl = $affectedUrl;
        $this->baseUrl = $baseUrl;
    }


    /**
     * @return void
     */
    public function modify(DOMNodeList $nodeList)
    {
        $affected = new Uri($this->affectedUrl);
        foreach ($nodeList as $element) {
            /** @var \DOMElement $element */
            $attribute = $this->attributeName($element);
            $relative = $element->getAttribute($attribute);
            $isValid = parse_url($relative) !== false;
            if (
                !preg_match('!^\s*javascript\s*:\s*!', $relative)
                and
                $isValid
            ) {
                if ($this->baseUrl !== '' and !preg_match('!^(/|http)!i', $relative)) {
                    $relative = UriResolver::resolve(new Uri($this->baseUrl), new Uri($relative));
                }
                $url = UriResolver::resolve($affected, new Uri($relative));
                $element->setAttribute($attribute, (string)$url);
            }
        }
    }


    private function attributeName(\DOMElement $element): string
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