<?php

declare(strict_types=1);

namespace Paysera\LoggingExtraBundle\Tests\Unit\Service\LogTracing;

use Paysera\LoggingExtraBundle\Service\LogTracing\TraceIdHttpClientDecorator;
use Paysera\LoggingExtraBundle\Service\LogTracing\TraceIdProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TraceIdHttpClientDecoratorTest extends TestCase
{
    private const TRACE_ID_HEADER = 'X-Trace-Id';
    private const TRACE_ID_VALUE = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    private $decorator;
    private $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClientInterface::class);

        $traceIdProvider = $this->createMock(TraceIdProvider::class);
        $traceIdProvider
            ->method('getTraceId')
            ->willReturn(self::TRACE_ID_VALUE)
        ;

        $this->decorator = new TraceIdHttpClientDecorator(
            $traceIdProvider,
            self::TRACE_ID_HEADER,
            $this->httpClient
        );
    }

    public function testSendRequestAddsCustomHeader(): void
    {
        $url = 'https://api.paysera.com';
        $options = [
            'headers' => [
                'Authorization' => 'Bearer mock-token'
            ]
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo($url),
                $this->equalTo(
                    [
                        'headers' => [
                            'Authorization' => 'Bearer mock-token',
                            self::TRACE_ID_HEADER => self::TRACE_ID_VALUE,
                        ],
                    ]
                )
            )
            ->willReturn($this->createMock(ResponseInterface::class));

        $this->decorator->request('GET', $url, $options);
    }

    public function testSendRequestAddsCustomHeaderIfHeadersNotExists(): void
    {
        $url = 'https://api.paysera.com';

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo($url),
                $this->equalTo(
                    [
                        'headers' => [
                            self::TRACE_ID_HEADER => self::TRACE_ID_VALUE,
                        ]
                    ]
                )
            )
            ->willReturn($this->createMock(ResponseInterface::class));

        $this->decorator->request('GET', $url, []);
    }
}
