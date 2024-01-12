<?php

declare(strict_types=1);

namespace Xparse\Parser;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Xparse\ElementFinder\ElementFinder;
use Xparse\ElementFinder\ElementFinderInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class Parser implements ParserInterface
{
    /**
     * @var ElementFinderInterface|null
     */
    protected $lastPage;

    /**
     * @var ResponseInterface|null
     */
    protected $lastResponse;

    public function __construct(
        protected ClientInterface $client = new Client([
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::COOKIES => new CookieJar(),
        ]),
        protected ElementFinderFactoryInterface $elementFinderFactory = new ElementFinderFactory()
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $url, array $options = []): ElementFinderInterface
    {
        if ($url === '') {
            throw new InvalidArgumentException('Url can\'t be empty');
        }
        return $this->send(
            new Request('GET', $url),
            $options
        );
    }

    public function send(RequestInterface $request, array $options = []): ElementFinderInterface
    {
        $prevCallback = ! empty($options['on_stats']) ? $options['on_stats'] : null;

        $effectiveUrl = '';
        $options['on_stats'] = function (TransferStats $stats) use (&$effectiveUrl, $prevCallback): void {
            $effectiveUrl = $stats->getEffectiveUri()->__toString();
            if ($prevCallback !== null) {
                /** @var callable $prevCallback */
                $prevCallback($stats);
            }
        };

        $response = $this->client->send($request, $options);

        $guzzleEffectiveUrl = $response->getHeaderLine('X-GUZZLE-EFFECTIVE-URL');
        if ($guzzleEffectiveUrl !== '') {
            $effectiveUrl = $guzzleEffectiveUrl;
        }

        $elementFinder = $this->elementFinderFactory->create($response, $effectiveUrl);
        $this->lastPage = $elementFinder;
        $this->lastResponse = $response;

        return $elementFinder;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function post(string $url, array $options = []): ElementFinderInterface
    {
        if ($url === '') {
            throw new InvalidArgumentException('Url can\'t be empty');
        }
        return $this->send(
            new Request('POST', $url),
            $options
        );
    }

    public function getLastPage(): ?ElementFinderInterface
    {
        return $this->lastPage;
    }

    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function getElementFinderFactory(): ElementFinderFactoryInterface
    {
        return $this->elementFinderFactory;
    }
}
