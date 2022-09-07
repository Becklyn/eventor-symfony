<?php

namespace Becklyn\Eventor\Application;

use CloudEvents\V1\CloudEventInterface;
use Symfony\Component\HttpFoundation\Request;

class TraceHeaders
{
    public static function createFromRequest(Request $request): self
    {
        return new self(
            $request->headers->get("traceparent", "00-00000000000000000000000000000000-0000000000000000-00"),
            $request->headers->get("tracestate"),
        );
    }

    public static function createFromCloudEvent(CloudEventInterface $event): self
    {
        return new self(
            $event->getExtension("traceparent") ?? "00-00000000000000000000000000000000-0000000000000000-00",
            $event->getExtension("tracestate"),
        );
    }

    public function __construct(
        public readonly string $traceParent,
        public readonly ?string $traceState,
    ) {}
}