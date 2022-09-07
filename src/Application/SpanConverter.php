<?php

namespace Becklyn\Eventor\Application;

use OpenTelemetry\API\Trace\SpanContext;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\TraceState;
use OpenTelemetry\Context\Context;
use OpenTelemetry\SDK\Trace\Span;

final class SpanConverter
{
    private function __construct()
    {}

    public static function fromHeaders(TraceHeaders $traceHeaders): SpanInterface
    {
        $traceParentParts = \explode("-", $traceHeaders->traceParent);

        $spanContext = SpanContext::createFromRemoteParent(
            $traceParentParts[1],
            $traceParentParts[2],
            (int) $traceParentParts[3],
            new TraceState($traceHeaders->traceState),
        );

        $span = Span::wrap($spanContext);
        $span->storeInContext(Context::getCurrent());
        return $span;
    }

    public static function toHeaders(SpanInterface $span): TraceHeaders
    {
        $spanContext = $span->getContext();

        $traceParent = "00-{$spanContext->getTraceId()}-{$spanContext->getSpanId()}-0{$spanContext->getTraceFlags()}";
        $traceState = (string) $spanContext->getTraceState();

        return new TraceHeaders($traceParent, $traceState);
    }
}