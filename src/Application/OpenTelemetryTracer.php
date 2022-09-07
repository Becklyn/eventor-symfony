<?php

namespace Becklyn\Eventor\Application;

use GuzzleHttp\Psr7\HttpFactory;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Contrib\OtlpHttp\Exporter as OtlpExporter;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Psr\Http\Client\ClientInterface;

class OpenTelemetryTracer implements Tracer
{
    private readonly TracerProviderInterface $tracerProvider;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly HttpFactory $httpFactory,
    ) {
        $exporter = new OtlpExporter(
            $this->httpClient,
            $this->httpFactory,
            $this->httpFactory,
        );

        $this->tracerProvider = new TracerProvider(
            [new SimpleSpanProcessor($exporter)],
            new AlwaysOnSampler(),
        );
    }

    public function trace(string $name): TracerInterface {
        return $this->tracerProvider->getTracer($name);
    }
}
