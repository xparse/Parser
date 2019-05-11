<?php

declare(strict_types=1);

namespace Xparse\Parser\Helper;
/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
interface EncodingConverterInterface
{

    /**
     * Try to detect input encoding from contentType or from html <meta> tag
     */
    public function convert(string $html, string $contentType): string;

}