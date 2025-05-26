<?php

declare(strict_types=1);

namespace Paysera\LoggingExtraBundle\Service\LogTracing;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TraceIdHttpClientDecorator
{
    private $traceIdProvider;
    private $traceIdHeaderName;
    private $client;

    public function __construct(
        TraceIdProvider $traceIdProvider,
        HttpClientInterface $client,
        string $traceIdHeaderName
    ) {
        $this->traceIdProvider = $traceIdProvider;
        $this->client = $client;
        $this->traceIdHeaderName = $traceIdHeaderName;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers'] = $options['headers'] ?? [];
        $options['headers'][$this->traceIdHeaderName] = $this->traceIdProvider->getTraceId();

        return $this->client->request($method, $url, $options);
    }
}
