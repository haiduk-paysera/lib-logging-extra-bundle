<?php

declare(strict_types=1);

namespace Paysera\LoggingExtraBundle\Service\LogTracing;

use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 *
 * Provides trace ID functionality for distributed tracing.
 * The trace ID is either retrieved from the request headers or generated as a UUID.
 */
class TraceIdProvider
{
    private $requestStack;
    private $traceIdHeaderName;
    private $traceId;

    public function __construct(
        RequestStack $requestStack,
        string $traceIdHeader
    ) {
        $this->requestStack = $requestStack;
        $this->traceIdHeaderName = $traceIdHeader;
        $this->traceId = null;
    }

    public function getTraceId(): string
    {
        if ($this->traceId !== null) {
            return $this->traceId;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            /**
             * Covers case when non-HTTP program (e.g. worker, cli-command) sends HTTP request
             * The outgoing HTTP request will be traced
             */
            $this->traceId = $this->generateTraceId();
            return $this->traceId;
        }

        $headerValue = $request->headers->get($this->traceIdHeaderName);
        if ($headerValue !== null && Uuid::isValid($headerValue)) {
            $this->traceId = $headerValue;
        } else {
            $this->traceId = $this->generateTraceId();
        }

        return $this->traceId;
    }

    private function generateTraceId(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function reset(): void
    {
        $this->traceId = null;
    }
}
