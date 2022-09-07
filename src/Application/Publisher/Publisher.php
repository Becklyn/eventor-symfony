<?php declare(strict_types=1);

namespace Becklyn\Eventor\Application\Publisher;

use OpenTelemetry\API\Trace\SpanInterface;

interface Publisher
{
    public function publish(string $topic, mixed $data, ?SpanInterface $span = null) : void;
}
