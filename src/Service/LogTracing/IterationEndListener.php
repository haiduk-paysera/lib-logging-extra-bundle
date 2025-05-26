<?php

declare(strict_types=1);

namespace Paysera\LoggingExtraBundle\Service\LogTracing;

class IterationEndListener
{
    private $traceIdProvider;

    public function __construct(TraceIdProvider $traceIdProvider)
    {
        $this->traceIdProvider = $traceIdProvider;
    }

    public function afterIteration()
    {
        $this->traceIdProvider->reset();
    }
}
