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
        string $traceIdHeaderName,
        HttpClientInterface $client
    ) {
        $this->traceIdProvider = $traceIdProvider;
        $this->traceIdHeaderName = $traceIdHeaderName;
        $this->client = $client;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers'] = $options['headers'] ?? [];
        $options['headers'][$this->traceIdHeaderName] = $this->traceIdProvider->getTraceId();

        return $this->client->request($method, $url, $options);
    }
}
