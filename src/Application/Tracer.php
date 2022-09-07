<?php

namespace Becklyn\Eventor\Application;

use OpenTelemetry\API\Trace\TracerInterface;

interface Tracer
{
    public function trace(string $name) : TracerInterface;
}