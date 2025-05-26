<?php

declare(strict_types=1);

namespace Paysera\LoggingExtraBundle\Service\LogTracing;

/**
 * @internal
 */
class TraceIdProcessor {
    private $traceIdProvider;

    public function __construct(TraceIdProvider $traceIdProvider)
    {
        $this->traceIdProvider = $traceIdProvider;
    }

        public function __invoke(array $record)
    {
        $record['extra']['trace_id'] = $this->traceIdProvider->getTraceId();
        return $record;
    }
}
