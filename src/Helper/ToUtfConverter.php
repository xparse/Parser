<?php

declare(strict_types=1);

namespace Xparse\Parser\Helper;

/**
 * Try to convert input encoding
 *
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ToUtfConverter implements EncodingConverterInterface
{
    private const EXCLUDED_ENCODINGS = [
        "utf-8",
        "utf8",
        "base64",
        "uuencode",
        "quoted-printable",
        "html-entities",
    ];

    /**
     * @var null|string[]
     */
    private $supportedEncodings;

    public function convert(string $html, string $contentType = ''): string
    {
        $encoding = '';
        if (preg_match('!^.*charset=([A-Za-z0-9-]{4,})$!', $contentType, $contentTypeData) === 1) {
            $encoding = $contentTypeData[1];
        } elseif (preg_match("!.*<meta.*charset=[\"']?[ \t]*([A-Za-z0-9-]{4,})[ \t]*[\"']!mi", $html, $metaContentType) === 1) {
            $encoding = $metaContentType[1];
        }
        $encoding = mb_strtolower($encoding);
        if ($encoding !== '' && in_array($encoding, $this->getSupportedEncodings(), true)) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $html = mb_convert_encoding($html, 'utf-8', $encoding);
        }

        return $html;
    }

    private function getSupportedEncodings(): array
    {
        if ($this->supportedEncodings === null) {
            $this->supportedEncodings = [];
            $findAliases = function_exists('mb_encoding_aliases');
            foreach (mb_list_encodings() as $encoding) {
                $encoding = mb_strtolower($encoding);
                if (! in_array($encoding, self::EXCLUDED_ENCODINGS)) {
                    $this->supportedEncodings[] = $encoding;
                    if ($findAliases) {
                        foreach (mb_encoding_aliases($encoding) as $encodingAlias) {
                            $this->supportedEncodings[] = mb_strtolower($encodingAlias);
                        }
                    }
                }
            }
        }
        return $this->supportedEncodings;
    }
}
